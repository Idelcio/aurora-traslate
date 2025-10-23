#!/usr/bin/env python3
"""
Create a SIMPLE PDF with just translated text.
No fancy layout - just clean, readable translated content.
"""

import argparse
import json
import sys
import textwrap

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

    # Page dimensions (A4)
    page_width = 595
    page_height = 842

    # Margins in points (~1.8cm each side)
    left_margin = 50
    right_margin = 50
    top_margin = 50
    bottom_margin = 50

    # Usable area width
    usable_width = page_width - left_margin - right_margin

    # Font settings
    font_size = 11
    line_height = font_size * 1.6  # 1.6 line spacing for readability

    # Create first page
    page = doc.new_page(width=page_width, height=page_height)
    current_y = top_margin

    # Process each page of translated content
    page_count = 0
    for page_data in translated.get("pages", []):
        page_count += 1

        # Get all translated texts from this page
        text_items = page_data.get("textItems", [])

        # Combine all text items with paragraph breaks
        full_text = ""
        for item in text_items:
            translated_text = item.get("translatedText", "").strip()
            if translated_text:
                full_text += translated_text + "\n\n"

        if not full_text.strip():
            continue

        # Split text into paragraphs
        paragraphs = [p.strip() for p in full_text.split('\n\n') if p.strip()]

        for paragraph in paragraphs:
            # Wrap text to fit within usable width
            # Approximate: 60 characters per line for 11pt font
            wrapped_lines = textwrap.wrap(paragraph, width=70)

            for line in wrapped_lines:
                # Check if we need a new page
                if current_y + line_height > page_height - bottom_margin:
                    page = doc.new_page(width=page_width, height=page_height)
                    current_y = top_margin

                # Insert line of text
                try:
                    page.insert_text(
                        (left_margin, current_y),
                        line,
                        fontsize=font_size,
                        fontname="helv",
                        color=(0, 0, 0),
                    )
                except Exception as e:
                    print(f"Warning: Failed to insert line: {e}", file=sys.stderr)

                # Move to next line
                current_y += line_height

            # Add extra spacing after paragraph
            current_y += line_height * 0.5

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
