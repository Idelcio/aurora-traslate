#!/usr/bin/env python3
"""
Concurrent translation using threading (works on Windows web servers).
This version avoids asyncio which has issues with Windows socket providers.
"""

from __future__ import annotations
import argparse
import json
import logging
import os
import sys
import time
import socket
from typing import Dict, List, Optional, Sequence, Tuple
from concurrent.futures import ThreadPoolExecutor, as_completed
import requests

# Force IPv4
def force_ipv4():
    def allowed_gai_family():
        return socket.AF_INET
    try:
        import requests.packages.urllib3.util.connection as urllib3_cn
        urllib3_cn.allowed_gai_family = allowed_gai_family
    except:
        pass

force_ipv4()

GOOGLE_TRANSLATE_ENDPOINT = "https://translation.googleapis.com/language/translate/v2"
MAX_CHARS_PER_BATCH = 30000


class TranslationError(Exception):
    """Raised when the translation API returns a non-success response."""


def normalise_text(text: str) -> str:
    """Standardise spacing without losing intentional line breaks."""
    if not text:
        return ""
    normalised_lines = [" ".join(line.split()) for line in text.splitlines()]
    return "\n".join(filter(None, normalised_lines)) or text.strip()


def create_smart_batches(texts: List[str], max_chars: int = MAX_CHARS_PER_BATCH) -> List[List[str]]:
    """Create batches dynamically based on character count."""
    batches = []
    current_batch = []
    current_chars = 0

    for text in texts:
        text_len = len(text)
        if current_batch and (current_chars + text_len > max_chars):
            batches.append(current_batch)
            current_batch = []
            current_chars = 0
        current_batch.append(text)
        current_chars += text_len

    if current_batch:
        batches.append(current_batch)

    return batches


def translate_batch(
    payloads: Sequence[str],
    target_language: str,
    source_language: Optional[str],
    api_key: str,
    timeout: float,
) -> List[str]:
    """Translate a batch of texts using Google Translate API."""
    url = f"{GOOGLE_TRANSLATE_ENDPOINT}?key={api_key}"
    body = {"q": payloads, "target": target_language, "format": "text"}
    if source_language:
        body["source"] = source_language

    # DEBUG: Log request details
    logging.debug(f"API Request - URL: {url[:50]}...")
    logging.debug(f"API Request - Body: target={target_language}, source={source_language}, texts={len(payloads)}")
    logging.debug(f"API Request - First text sample: {payloads[0][:50] if payloads else 'N/A'}")

    try:
        response = requests.post(url, json=body, timeout=timeout)
    except requests.RequestException as exc:
        raise TranslationError(f"Request failed: {exc}") from exc

    if response.status_code != 200:
        raise TranslationError(f"Google API error {response.status_code}: {response.text}")

    try:
        data = response.json()
    except Exception as exc:
        raise TranslationError(f"Invalid JSON from API: {exc}")

    translations = data.get("data", {}).get("translations")
    if not translations or len(translations) != len(payloads):
        raise TranslationError(f"Unexpected response structure: {data}")

    results = [t.get("translatedText", "") for t in translations]

    # DEBUG: Log response sample
    if results:
        logging.debug(f"API Response - First translation: '{payloads[0][:30]}' -> '{results[0][:30]}'")

    return results


def translate_texts_concurrent(
    texts: Sequence[str],
    *,
    target_language: str,
    source_language: Optional[str],
    api_key: str,
    timeout: float,
    retry_attempts: int,
    retry_backoff: float,
    max_workers: int = 5,
) -> Dict[str, str]:
    """Translate texts concurrently using ThreadPoolExecutor."""

    # Pre-normalize to reduce duplicates
    normalized_map = {text: normalise_text(text) for text in texts if text}
    cache: Dict[str, str] = {}
    unique_normalized = list(set(normalized_map.values()))

    logging.info(
        "Deduplication: %d original texts -> %d unique normalized texts (%.1f%% reduction)",
        len(texts),
        len(unique_normalized),
        (1 - len(unique_normalized) / max(len(texts), 1)) * 100
    )

    # Create smart batches
    batches = create_smart_batches(unique_normalized)
    total_batches = len(batches)

    logging.info("Created %d smart batches from %d unique texts", total_batches, len(unique_normalized))

    def process_batch(batch: List[str]) -> Dict[str, str]:
        """Process a single batch with retry logic."""
        attempts = 0
        while True:
            attempts += 1
            try:
                translations = translate_batch(
                    batch,
                    target_language=target_language,
                    source_language=source_language,
                    api_key=api_key,
                    timeout=timeout,
                )
                return {original: translated for original, translated in zip(batch, translations)}
            except TranslationError as exc:
                logging.warning("Batch failed (attempt %d/%d): %s", attempts, retry_attempts, exc)
                if attempts >= retry_attempts:
                    logging.error("Giving up on batch after %d attempts", attempts)
                    return {original: original for original in batch}
                time.sleep(retry_backoff * attempts)

    # Process batches concurrently
    with ThreadPoolExecutor(max_workers=max_workers) as executor:
        futures = {executor.submit(process_batch, batch): batch for batch in batches}

        for future in as_completed(futures):
            result = future.result()
            cache.update(result)

    # Map back to original texts
    final_cache = {}
    for original_text, normalized_text in normalized_map.items():
        if normalized_text in cache:
            final_cache[original_text] = cache[normalized_text]
        else:
            final_cache[original_text] = original_text

    return final_cache


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
            flattened.append({
                "page_index": page_index,
                "item_index": item_index,
                "text": text,
            })
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
        output["pages"][page_idx]["textItems"].append({
            "originalText": text,
            "translatedText": normalise_text(translated) or text,
        })
    return output


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Translate extracted PDF JSON using Google Translate API (threading version)."
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
    parser.add_argument("--log-level", default="INFO")

    args = parser.parse_args()
    logging.basicConfig(
        level=args.log_level.upper(),
        format="%(asctime)s %(levelname)s %(message)s",
    )

    api_key = args.api_key or os.getenv("GOOGLE_TRANSLATE_API_KEY") or os.getenv("GOOGLE_API_KEY")
    if not api_key:
        logging.error("Google Translate API key not provided.")
        return 2

    start_time = time.time()

    try:
        extracted = load_json(args.input_json)
        flattened, texts = collect_text_items(extracted)
        total_items = len(flattened)
        total_chars = sum(len(text or "") for text in texts)

        logging.info("Processing %d text items (%d chars) across %d page(s)",
                    total_items, total_chars, extracted.get("numPages"))

        translations = translate_texts_concurrent(
            texts,
            target_language=args.target_language,
            source_language=args.source_language,
            api_key=api_key,
            timeout=args.request_timeout,
            retry_attempts=args.retry_attempts,
            retry_backoff=args.retry_backoff,
            max_workers=args.max_concurrent,
        )

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

    except Exception as exc:
        import traceback
        logging.error("Translation failed: %s", exc)
        print(f"ERROR: {exc}")
        print(traceback.format_exc())
        return 3


if __name__ == "__main__":
    sys.exit(main())
