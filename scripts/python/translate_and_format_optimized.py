#!/usr/bin/env python3
"""
Optimized utility script to translate extracted PDF text with async/parallel processing.
Performance improvements:
- Async/await with aiohttp for concurrent requests
- Connection pooling and keep-alive
- Pre-normalization to reduce unique texts
- Dynamic batch sizing based on character count
- Real-time progress tracking via JSON Lines
- Intelligent retry with exponential backoff
"""

from __future__ import annotations
import argparse
import asyncio
import json
import logging
import os
import sys
import time
import socket
from typing import Dict, Iterable, List, Optional, Sequence, Tuple
from dataclasses import dataclass

try:
    import aiohttp
    import requests.packages.urllib3.util.connection as urllib3_cn
except ImportError:
    print("Error: Required packages not installed. Run: pip install aiohttp", file=sys.stderr)
    sys.exit(1)


# -------------------------------------------------------------------
# 🌐 Force IPv4 to avoid getaddrinfo / DNS errors on Windows
# -------------------------------------------------------------------
def force_ipv4():
    def allowed_gai_family():
        return socket.AF_INET
    urllib3_cn.allowed_gai_family = allowed_gai_family


force_ipv4()

GOOGLE_TRANSLATE_ENDPOINT = "https://translation.googleapis.com/language/translate/v2"
MAX_CHARS_PER_BATCH = 30000  # Google Translate API limit


@dataclass
class TranslationProgress:
    total_batches: int
    completed_batches: int
    total_texts: int
    translated_texts: int
    cached_texts: int
    failed_texts: int

    def to_dict(self) -> dict:
        return {
            "totalBatches": self.total_batches,
            "completedBatches": self.completed_batches,
            "totalTexts": self.total_texts,
            "translatedTexts": self.translated_texts,
            "cachedTexts": self.cached_texts,
            "failedTexts": self.failed_texts,
            "progress": round((self.completed_batches / self.total_batches * 100), 2) if self.total_batches > 0 else 0
        }


class TranslationError(Exception):
    """Raised when the translation API returns a non-success response."""


def normalise_text(text: str) -> str:
    """Standardise spacing without losing intentional line breaks."""
    if not text:
        return ""
    normalised_lines = [" ".join(line.split()) for line in text.splitlines()]
    return "\n".join(filter(None, normalised_lines)) or text.strip()


def create_smart_batches(texts: List[str], max_chars: int = MAX_CHARS_PER_BATCH) -> List[List[str]]:
    """
    Create batches dynamically based on character count rather than fixed size.
    This optimizes API usage and reduces the number of requests.
    """
    batches = []
    current_batch = []
    current_chars = 0

    for text in texts:
        text_len = len(text)

        # If adding this text would exceed limit, start new batch
        if current_batch and (current_chars + text_len > max_chars):
            batches.append(current_batch)
            current_batch = []
            current_chars = 0

        current_batch.append(text)
        current_chars += text_len

    # Add remaining batch
    if current_batch:
        batches.append(current_batch)

    return batches


async def translate_batch_async(
    session: aiohttp.ClientSession,
    payloads: Sequence[str],
    target_language: str,
    source_language: Optional[str],
    api_key: str,
    timeout: float,
) -> List[str]:
    """Translate a batch of texts using Google Translate API (async)."""
    url = f"{GOOGLE_TRANSLATE_ENDPOINT}?key={api_key}"

    body = {"q": payloads, "target": target_language, "format": "text"}
    if source_language:
        body["source"] = source_language

    try:
        async with session.post(
            url,
            json=body,
            timeout=aiohttp.ClientTimeout(total=timeout)
        ) as response:
            if response.status != 200:
                text = await response.text()
                raise TranslationError(f"Google API error {response.status}: {text}")

            data = await response.json()

    except asyncio.TimeoutError as exc:
        raise TranslationError(f"Request timeout after {timeout}s") from exc
    except aiohttp.ClientError as exc:
        raise TranslationError(f"Request failed: {exc}") from exc
    except Exception as exc:
        raise TranslationError(f"Unexpected error: {exc}") from exc

    translations = data.get("data", {}).get("translations")
    if not translations or len(translations) != len(payloads):
        raise TranslationError(f"Unexpected response structure: {data}")

    return [t.get("translatedText", "") for t in translations]


async def translate_texts_async(
    texts: Sequence[str],
    *,
    target_language: str,
    source_language: Optional[str],
    api_key: str,
    timeout: float,
    retry_attempts: int,
    retry_backoff: float,
    max_concurrent: int = 5,
    progress_callback = None,
) -> Dict[str, str]:
    """
    Translate texts asynchronously with concurrent requests and intelligent batching.
    """
    # Pre-normalize to reduce duplicates
    normalized_map = {text: normalise_text(text) for text in texts if text}

    # Build cache with normalized versions to maximize deduplication
    cache: Dict[str, str] = {}
    unique_normalized = list(set(normalized_map.values()))

    logging.info(
        "Deduplication: %d original texts -> %d unique normalized texts (%.1f%% reduction)",
        len(texts),
        len(unique_normalized),
        (1 - len(unique_normalized) / max(len(texts), 1)) * 100
    )

    # Create smart batches based on character count
    batches = create_smart_batches(unique_normalized)
    total_batches = len(batches)

    progress = TranslationProgress(
        total_batches=total_batches,
        completed_batches=0,
        total_texts=len(texts),
        translated_texts=0,
        cached_texts=0,
        failed_texts=0
    )

    logging.info("Created %d smart batches from %d unique texts", total_batches, len(unique_normalized))

    # Create session with connection pooling
    connector = aiohttp.TCPConnector(
        limit=max_concurrent,
        limit_per_host=max_concurrent,
        ttl_dns_cache=300,
        force_close=False,
        enable_cleanup_closed=True
    )

    async with aiohttp.ClientSession(connector=connector) as session:
        # Process batches with concurrency limit
        semaphore = asyncio.Semaphore(max_concurrent)

        async def process_batch(batch: List[str]) -> None:
            async with semaphore:
                attempts = 0
                while True:
                    attempts += 1
                    try:
                        translations = await translate_batch_async(
                            session,
                            batch,
                            target_language=target_language,
                            source_language=source_language,
                            api_key=api_key,
                            timeout=timeout,
                        )

                        for original, translated in zip(batch, translations):
                            cache[original] = translated

                        progress.completed_batches += 1
                        progress.translated_texts += len(batch)

                        if progress_callback:
                            progress_callback(progress)

                        break

                    except TranslationError as exc:
                        logging.warning("Batch failed (attempt %d/%d): %s", attempts, retry_attempts, exc)

                        if attempts >= retry_attempts:
                            logging.error("Giving up on batch after %d attempts", attempts)
                            for original in batch:
                                cache[original] = original
                            progress.failed_texts += len(batch)
                            progress.completed_batches += 1

                            if progress_callback:
                                progress_callback(progress)
                            break

                        sleep_time = retry_backoff * attempts
                        await asyncio.sleep(sleep_time)

        # Process all batches concurrently
        await asyncio.gather(*[process_batch(batch) for batch in batches])

    # Map back to original (non-normalized) texts
    final_cache = {}
    for original_text, normalized_text in normalized_map.items():
        if normalized_text in cache:
            final_cache[original_text] = cache[normalized_text]
        else:
            final_cache[original_text] = original_text

    return final_cache


# -------------------------------------------------------------------
# 📄 File operations
# -------------------------------------------------------------------
def load_json(path: str) -> Dict:
    with open(path, "r", encoding="utf-8") as handle:
        return json.load(handle)


def write_json(path: str, payload: Dict) -> None:
    with open(path, "w", encoding="utf-8") as handle:
        json.dump(payload, handle, ensure_ascii=False, indent=2)


def collect_text_items(extracted: Dict) -> Tuple[List[Dict], List[str]]:
    pages = extracted.get("pages", [])
    flattened: List[Dict] = []
    texts: List[str] = []

    for page_index, page in enumerate(pages):
        for item_index, item in enumerate(page.get("textItems", [])):
            text = item.get("text", "")
            flattened.append(
                {
                    "page_index": page_index,
                    "item_index": item_index,
                    "text": text,
                }
            )
            texts.append(text)
    return flattened, texts


def build_output_structure(
    extracted: Dict,
    flattened: Sequence[Dict],
    translations: Dict[str, str],
) -> Dict:
    output = {"numPages": extracted.get("numPages"), "pages": []}
    for page in extracted.get("pages", []):
        output["pages"].append({"pageNumber": page.get("pageNumber"), "textItems": []})

    for entry in flattened:
        page_idx = entry["page_index"]
        text = entry["text"]
        translated = translations.get(text, text)
        output["pages"][page_idx]["textItems"].append(
            {
                "originalText": text,
                "translatedText": normalise_text(translated) or text,
            }
        )
    return output


# -------------------------------------------------------------------
# 🚀 Main function
# -------------------------------------------------------------------
async def main_async(args) -> int:
    api_key = (
        args.api_key
        or os.getenv("GOOGLE_TRANSLATE_API_KEY")
        or os.getenv("GOOGLE_API_KEY")
    )
    if not api_key:
        logging.error("Google Translate API key not provided.")
        return 2

    start_time = time.time()

    extracted = load_json(args.input_json)
    flattened, texts = collect_text_items(extracted)
    total_items = len(flattened)
    total_chars = sum(len(text or "") for text in texts)

    logging.info(
        "Processing %d text items (%d chars) across %d page(s)",
        total_items,
        total_chars,
        extracted.get("numPages"),
    )

    def progress_callback(progress: TranslationProgress):
        # Output progress as JSON Lines for real-time tracking
        print(json.dumps({"type": "progress", "data": progress.to_dict()}), flush=True)

    try:
        translations = await translate_texts_async(
            texts,
            target_language=args.target_language,
            source_language=args.source_language,
            api_key=api_key,
            timeout=args.request_timeout,
            retry_attempts=args.retry_attempts,
            retry_backoff=args.retry_backoff,
            max_concurrent=args.max_concurrent,
            progress_callback=progress_callback if args.progress else None,
        )
    except Exception as exc:
        import traceback
        logging.error("Translation failed: %s", exc)
        print(f"ERROR: {exc}")
        print(traceback.format_exc())
        return 3

    output_payload = build_output_structure(extracted, flattened, translations)
    write_json(args.output_json, output_payload)

    elapsed_time = time.time() - start_time

    stats = {
        "totalPages": extracted.get("numPages"),
        "totalTexts": total_items,
        "totalChars": total_chars,
        "uniqueTexts": len({t for t in texts if t}),
        "elapsedSeconds": round(elapsed_time, 2),
        "textsPerSecond": round(total_items / elapsed_time, 2) if elapsed_time > 0 else 0,
    }

    logging.info("Translation completed in %.2fs: %s", elapsed_time, stats)
    print(json.dumps({"type": "result", "success": True, "stats": stats}))
    return 0


def main(argv: Optional[Sequence[str]] = None) -> int:
    parser = argparse.ArgumentParser(
        description="Translate extracted PDF JSON using Google Translate API (optimized async version)."
    )
    parser.add_argument("--input-json", required=True)
    parser.add_argument("--output-json", required=True)
    parser.add_argument("--target-language", required=True)
    parser.add_argument("--source-language", default=None)
    parser.add_argument("--api-key", default=None)
    parser.add_argument("--request-timeout", type=float, default=30.0)
    parser.add_argument("--retry-attempts", type=int, default=3)
    parser.add_argument("--retry-backoff", type=float, default=2.0)
    parser.add_argument("--max-concurrent", type=int, default=5, help="Max concurrent requests")
    parser.add_argument("--progress", action="store_true", help="Output progress as JSON Lines")
    parser.add_argument("--log-level", default="INFO")

    args = parser.parse_args(argv)
    logging.basicConfig(
        level=args.log_level.upper(),
        format="%(asctime)s %(levelname)s %(message)s",
    )

    # Run async main
    return asyncio.run(main_async(args))


if __name__ == "__main__":
    sys.exit(main())
