#!/usr/bin/env python3
"""
Utility script to translate extracted PDF text and keep formatting metadata.
"""

from __future__ import annotations
import argparse
import json
import logging
import os
import sys
import time
import socket
from typing import Dict, Iterable, List, Optional, Sequence, Tuple
import requests
import requests.packages.urllib3.util.connection as urllib3_cn


# -------------------------------------------------------------------
# 🌐 Force IPv4 to avoid getaddrinfo / DNS errors on Windows
# -------------------------------------------------------------------
def force_ipv4():
    def allowed_gai_family():
        return socket.AF_INET

    urllib3_cn.allowed_gai_family = allowed_gai_family


force_ipv4()

GOOGLE_TRANSLATE_ENDPOINT = "https://translation.googleapis.com/language/translate/v2"


class TranslationError(Exception):
    """Raised when the translation API returns a non-success response."""


def chunked(sequence: Sequence[str], size: int) -> Iterable[Sequence[str]]:
    for idx in range(0, len(sequence), size):
        yield sequence[idx : idx + size]


def normalise_text(text: str) -> str:
    """Standardise spacing without losing intentional line breaks."""
    if not text:
        return ""
    normalised_lines = [" ".join(line.split()) for line in text.splitlines()]
    return "\n".join(filter(None, normalised_lines)) or text.strip()


# -------------------------------------------------------------------
# 🧠 Main translation logic
# -------------------------------------------------------------------
def translate_batch(
    payloads: Sequence[str],
    target_language: str,
    api_key: str,
    timeout: float,
) -> List[str]:
    """Translate a batch of texts using Google Translate API (JSON).

    No source language specified - Google will auto-detect for multi-language support.
    """
    url = f"{GOOGLE_TRANSLATE_ENDPOINT}?key={api_key}"

    # Don't include source language - let Google auto-detect for multi-language support
    body = {"q": payloads, "target": target_language, "format": "text"}

    try:
        response = requests.post(url, json=body, timeout=timeout)
    except requests.RequestException as exc:
        raise TranslationError(f"Request failed: {exc}") from exc

    if response.status_code != 200:
        raise TranslationError(
            f"Google API error {response.status_code}: {response.text}"
        )

    try:
        data = response.json()
    except Exception as exc:
        raise TranslationError(f"Invalid JSON from API: {exc}")

    translations = data.get("data", {}).get("translations")
    if not translations or len(translations) != len(payloads):
        raise TranslationError(f"Unexpected response structure: {data}")

    return [t.get("translatedText", "") for t in translations]


def translate_texts(
    texts: Sequence[str],
    *,
    target_language: str,
    api_key: str,
    batch_size: int,
    timeout: float,
    retry_attempts: int,
    retry_backoff: float,
) -> Dict[str, str]:
    cache: Dict[str, str] = {}
    unique_texts = [t for t in texts if t and t not in cache]

    for chunk in chunked(unique_texts, batch_size):
        attempts = 0
        while True:
            attempts += 1
            try:
                translations = translate_batch(
                    chunk,
                    target_language=target_language,
                    api_key=api_key,
                    timeout=timeout,
                )
                for original, translated in zip(chunk, translations):
                    # Keep original text if translation is empty or failed
                    cache[original] = translated if translated else original
                break
            except TranslationError as exc:
                logging.warning("Failed to translate batch: %s", exc)
                if attempts >= retry_attempts:
                    logging.warning(
                        "Giving up on batch after %s attempts; keeping original text",
                        attempts,
                    )
                    # Keep original text for untranslatable content
                    for original in chunk:
                        cache[original] = original
                    break
                sleep_time = retry_backoff * attempts
                logging.info(
                    "Retrying batch (%s/%s) in %.1fs",
                    attempts,
                    retry_attempts,
                    sleep_time,
                )
                time.sleep(sleep_time)

    return cache


# -------------------------------------------------------------------
# 📄 File operations
# -------------------------------------------------------------------
def load_json(path: str) -> Dict:
    with open(path, "r", encoding="utf-8") as handle:
        return json.load(handle)


def write_json(path: str, payload: Dict) -> None:
    with open(path, "w", encoding="utf-8") as handle:
        json.dump(payload, handle, ensure_ascii=False, indent=2)


def collect_text_items(extracted: Dict) -> Tuple[List[Dict], List[str], List[Dict]]:
    """
    Collect text items from pages only (outline will be copied as-is).
    Returns: (flattened_pages, page_texts, outline_items)
    """
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

    # Get outline but DON'T translate it - keep original
    outline = extracted.get("outline", [])

    return flattened, texts, outline


def build_output_structure(
    extracted: Dict,
    flattened: Sequence[Dict],
    outline_items: List[Dict],
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

    # Copy outline as-is (no translation)
    output["outline"] = outline_items

    return output


# -------------------------------------------------------------------
# 🚀 Main function
# -------------------------------------------------------------------
def main(argv: Optional[Sequence[str]] = None) -> int:
    parser = argparse.ArgumentParser(
        description="Translate extracted PDF JSON using Google Translate API."
    )
    parser.add_argument("--input-json", required=True)
    parser.add_argument("--output-json", required=True)
    parser.add_argument("--target-language", required=True)
    parser.add_argument("--api-key", default=None)
    parser.add_argument("--batch-size", type=int, default=64)
    parser.add_argument("--request-timeout", type=float, default=15.0)
    parser.add_argument("--retry-attempts", type=int, default=3)
    parser.add_argument("--retry-backoff", type=float, default=2.5)
    parser.add_argument("--log-level", default="INFO")

    args = parser.parse_args(argv)
    logging.basicConfig(
        level=args.log_level.upper(),
        format="%(asctime)s %(levelname)s %(message)s",
    )

    api_key = (
        args.api_key
        or os.getenv("GOOGLE_TRANSLATE_API_KEY")
        or os.getenv("GOOGLE_API_KEY")
    )
    if not api_key:
        logging.error("Google Translate API key not provided.")
        return 2

    extracted = load_json(args.input_json)
    flattened, texts, outline_items = collect_text_items(extracted)
    total_items = len(flattened)
    total_chars = sum(len(text or "") for text in texts)

    logging.info(
        "Processing %s text items across %s page(s) (outline: %s entries, kept as-is)",
        total_items,
        extracted.get("numPages"),
        len(outline_items),
    )

    try:
        translations = translate_texts(
            texts,
            target_language=args.target_language,
            api_key=api_key,
            batch_size=args.batch_size,
            timeout=args.request_timeout,
            retry_attempts=args.retry_attempts,
            retry_backoff=args.retry_backoff,
        )
    except TranslationError as exc:
        logging.error("Translation failed: %s", exc)
        return 3

    output_payload = build_output_structure(extracted, flattened, outline_items, translations)
    write_json(args.output_json, output_payload)

    stats = {
        "totalPages": extracted.get("numPages"),
        "totalTexts": total_items,
        "totalChars": total_chars,
        "uniqueTexts": len({t for t in texts if t}),
    }

    logging.info("Translation completed successfully: %s", stats)
    print(json.dumps({"success": True, "stats": stats}))
    return 0


if __name__ == "__main__":
    sys.exit(main())
