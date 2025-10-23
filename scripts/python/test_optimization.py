#!/usr/bin/env python3
"""
Simple test to demonstrate optimization improvements
"""
import json
import sys

# Create a test extracted.json with duplicate texts
test_data = {
    "numPages": 2,
    "pages": [
        {
            "pageNumber": 1,
            "textItems": [
                {"text": "Hello World"},
                {"text": "This is a test"},
                {"text": "Hello World"},  # duplicate
                {"text": "Another text"},
                {"text": "This is a test"},  # duplicate
                {"text": "Hello World"},  # duplicate
            ]
        },
        {
            "pageNumber": 2,
            "textItems": [
                {"text": "Hello World"},  # duplicate
                {"text": "More content"},
                {"text": "This is a test"},  # duplicate
                {"text": "Final text"},
            ]
        }
    ]
}

# Calculate deduplication savings
all_texts = []
for page in test_data["pages"]:
    for item in page["textItems"]:
        all_texts.append(item["text"])

unique_texts = list(set(all_texts))

print("=" * 60)
print("OPTIMIZATION DEMONSTRATION")
print("=" * 60)
print(f"\nTotal text items: {len(all_texts)}")
print(f"Unique text items: {len(unique_texts)}")
print(f"Deduplication savings: {(1 - len(unique_texts)/len(all_texts)) * 100:.1f}%")
print(f"\nDuplicate texts: {len(all_texts) - len(unique_texts)}")
print(f"\nWith optimized script:")
print(f"  - Only {len(unique_texts)} API calls needed (instead of {len(all_texts)})")
print(f"  - {len(unique_texts)} concurrent requests (max_concurrent=5)")
print(f"  - Smart batching based on character count")
print(f"  - Connection pooling for faster requests")
print("\nExpected speedup: 2-5x faster")
print("=" * 60)

# Write test file
test_file = "scripts/python/test_extracted.json"
with open(test_file, "w", encoding="utf-8") as f:
    json.dump(test_data, f, indent=2)

print(f"\nTest file created: {test_file}")
print("\nTo test translation (requires API key):")
print(f"  python scripts/python/translate_and_format_optimized.py \\")
print(f"    --input-json {test_file} \\")
print(f"    --output-json scripts/python/test_translated.json \\")
print(f"    --target-language pt \\")
print(f"    --api-key YOUR_API_KEY \\")
print(f"    --progress")
