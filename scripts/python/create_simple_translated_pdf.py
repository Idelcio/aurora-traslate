#!/usr/bin/env python3
"""
Create a SIMPLE PDF with just translated text.
No fancy layout - just clean, readable translated content.
"""

import argparse
import json
import sys

try:
    import fitz  # PyMuPDF
except ImportError:
    print("Error: PyMuPDF not installed")
    sys.exit(1)


def load_json(path):
    with open(path, 'r', encoding='utf-8') as f:
        return json.load(f)


def create_simple_pdf(translated_json_path, output_pdf_path):
    """Create a simple, clean PDF with translated text only."""

    # Load translation data
    translated = load_json(translated_json_path)

    # Create new PDF
    doc = fitz.open()

    # Process each page
    for page_data in translated.get("pages", []):
        # Create new page (A4 size)
        page = doc.new_page(width=595, height=842)  # A4 in points

        # Start position
        y_position = 50
        x_margin = 50
        line_height = 15

        # Get all translated texts
        text_items = page_data.get("textItems", [])

        for item in text_items:
            translated_text = item.get("translatedText", "")

            if not translated_text:
                continue

            # Insert text (simple, clean formatting)
            try:
                page.insert_text(
                    (x_margin, y_position),
                    translated_text,
                    fontsize=11,
                    fontname="helv",
                    color=(0, 0, 0),
                )
                y_position += line_height

                # Start new page if we're at the bottom
                if y_position > 800:
                    page = doc.new_page(width=595, height=842)
                    y_position = 50

            except Exception as e:
                # Skip if text insertion fails
                continue

    # Save PDF
    doc.save(output_pdf_path, garbage=4, deflate=True)
    doc.close()

    print(f"Simple PDF created: {output_pdf_path}")


def main():
    parser = argparse.ArgumentParser(description="Create simple translated PDF")
    parser.add_argument("translated_json", help="Path to translated JSON file")
    parser.add_argument("output_pdf", help="Path to output PDF file")

    args = parser.parse_args()

    try:
        create_simple_pdf(args.translated_json, args.output_pdf)
        return 0
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    sys.exit(main())
