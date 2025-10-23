#!/usr/bin/env python3
"""
Create a completely NEW PDF with only translated text.
Uses the original PDF as background image.
"""

import argparse
import json
import sys
import os

try:
    import fitz  # PyMuPDF
except ImportError:
    print("Error: PyMuPDF not installed. Run: pip install PyMuPDF", file=sys.stderr)
    sys.exit(1)


def load_json(path):
    with open(path, 'r', encoding='utf-8') as f:
        return json.load(f)


def create_new_pdf(original_pdf_path, extracted_json_path, translated_json_path, output_pdf_path):
    """
    Create a brand new PDF with translated text.
    Uses original PDF pages as background images.
    """
    try:
        # Load translation data
        extracted = load_json(extracted_json_path)
        translated = load_json(translated_json_path)

        # Open original PDF
        original_doc = fitz.open(original_pdf_path)

        # COPY original PDF to start with
        new_doc = fitz.open()
        new_doc.insert_pdf(original_doc)

        # Process each page
        for page_idx, page_data in enumerate(translated.get("pages", [])):
            if page_idx >= len(new_doc):
                break

            # Get the page we just copied
            new_page = new_doc[page_idx]

            # FIRST: Remove ALL text from the page (keep only images/graphics)
            # This ensures we start fresh
            new_page.clean_contents()

            # Now add translated texts ON TOP
            text_items = page_data.get("textItems", [])
            extracted_page = extracted["pages"][page_idx]

            for item in text_items:
                original_text = item.get("originalText", "")
                translated_text = item.get("translatedText", "")

                # Skip if no translation or same as original
                if not translated_text or translated_text == original_text:
                    continue

                # Find position from extracted data
                position_item = None
                for ext_item in extracted_page.get("textItems", []):
                    if ext_item.get("text") == original_text:
                        position_item = ext_item
                        break

                if not position_item:
                    continue

                # Get position and size
                x = position_item.get("x", 0)
                y = position_item.get("y", 0)
                width = position_item.get("width", 100)
                height = position_item.get("height", 12)
                font_size = position_item.get("fontSize", 10)

                # Draw WHITE RECTANGLE to cover original text
                cover_rect = fitz.Rect(x - 2, y - height - 2, x + width * 1.5 + 100, y + 5)
                shape = new_page.new_shape()
                shape.draw_rect(cover_rect)
                shape.finish(fill=(1, 1, 1), color=None)  # White fill, no border
                shape.commit()

                # Insert TRANSLATED text
                new_page.insert_text(
                    (x, y),
                    translated_text,
                    fontsize=font_size,
                    fontname="helv",
                    color=(0, 0, 0),  # Black text
                )

        # Save the NEW PDF
        new_doc.save(output_pdf_path, garbage=4, deflate=True, clean=True)
        new_doc.close()
        original_doc.close()

        print(f"New PDF created successfully!")
        print(f"  Output: {output_pdf_path}")

    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        raise


def main():
    parser = argparse.ArgumentParser(
        description="Create new PDF with translated text"
    )
    parser.add_argument("original_pdf", help="Path to original PDF file")
    parser.add_argument("extracted_json", help="Path to extracted JSON file")
    parser.add_argument("translated_json", help="Path to translated JSON file")
    parser.add_argument("output_pdf", help="Path to output PDF file")

    args = parser.parse_args()

    try:
        create_new_pdf(
            args.original_pdf,
            args.extracted_json,
            args.translated_json,
            args.output_pdf
        )
        return 0
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    sys.exit(main())
