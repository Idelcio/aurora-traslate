#!/usr/bin/env python3
"""
Rebuild PDF with translated text overlays using PyMuPDF.
Python replacement for rebuildPdfWithTranslation.cjs with better performance.
"""

from __future__ import annotations
import argparse
import json
import sys
from typing import Dict, List

try:
    import fitz  # PyMuPDF
except ImportError:
    print("Error: PyMuPDF not installed. Run: pip install PyMuPDF", file=sys.stderr)
    sys.exit(1)


def load_json(path: str) -> Dict:
    """Load JSON file."""
    with open(path, "r", encoding="utf-8") as f:
        return json.load(f)


def rebuild_pdf_with_translation(
    original_pdf_path: str,
    extracted_json_path: str,
    translated_json_path: str,
    output_pdf_path: str,
    overlay_color: tuple = (0, 0, 0),  # Black
    background_color: tuple = (1, 1, 1),  # White
) -> None:
    """
    Create a new PDF with translated text in a clean layout.

    Strategy:
    1. Create new blank PDF pages with A4 dimensions (595 x 842 points ~ 21cm x 29.7cm)
    2. Write translated text with proper margins and line wrapping
    3. Use standard page size: 18cm x 28cm of usable area with margins
    """
    try:
        # Load translation data
        extracted = load_json(extracted_json_path)
        translated = load_json(translated_json_path)

        # Create a new PDF document
        doc = fitz.open()

        # Page dimensions in points (1 point = 1/72 inch)
        # A4: 595 x 842 points (21cm x 29.7cm)
        page_width = 595  # ~21cm
        page_height = 842  # ~29.7cm

        # Define margins (in points)
        left_margin = 50   # ~1.8cm
        right_margin = 50  # ~1.8cm
        top_margin = 50    # ~1.8cm
        bottom_margin = 50 # ~1.8cm

        # Usable area
        usable_width = page_width - left_margin - right_margin  # ~17.4cm
        usable_height = page_height - top_margin - bottom_margin  # ~26cm

        # Process each page
        for page_idx, page_data in enumerate(translated.get("pages", [])):
            # Create a new blank page
            page = doc.new_page(width=page_width, height=page_height)

            # Get translated text items
            text_items = page_data.get("textItems", [])

            # Current Y position for writing text
            current_y = top_margin

            for item in text_items:
                translated_text = item.get("translatedText", "")

                if not translated_text:
                    continue

                # Use a reasonable font size (12pt for body text)
                font_size = 12
                line_height = font_size * 1.5  # 1.5 line spacing

                # Check if we have enough space on current page
                # If not, we'll still write but let textbox handle overflow
                remaining_height = page_height - bottom_margin - current_y

                # Create text rectangle with full usable width
                text_rect = fitz.Rect(
                    left_margin,
                    current_y,
                    page_width - right_margin,
                    page_height - bottom_margin
                )

                try:
                    # Insert text with automatic word wrapping
                    # Returns the number of characters that didn't fit (negative if overflow)
                    rc = page.insert_textbox(
                        text_rect,
                        translated_text,
                        fontsize=font_size,
                        color=overlay_color,
                        fontname="helv",
                        align=fitz.TEXT_ALIGN_LEFT,
                    )

                    # Calculate how much vertical space was used
                    # Estimate lines used
                    chars_per_line = int(usable_width / (font_size * 0.5))
                    if chars_per_line > 0:
                        if rc >= 0:  # All text fit
                            text_length = len(translated_text)
                        else:  # Some overflow
                            text_length = len(translated_text) + rc  # rc is negative

                        lines_used = max(1, text_length // chars_per_line + 1)
                    else:
                        lines_used = 1

                    # Move cursor down for next text block
                    current_y += lines_used * line_height + 10  # Add 10pt spacing between blocks

                    # If text didn't fit, try with smaller font
                    if rc < 0:
                        # Clear the area and try again with smaller font
                        shape = page.new_shape()
                        shape.draw_rect(text_rect)
                        shape.finish(fill=background_color, color=None)
                        shape.commit()

                        # Reset cursor
                        current_y -= lines_used * line_height + 10

                        # Try with 10pt font
                        smaller_font = 10
                        text_rect = fitz.Rect(
                            left_margin,
                            current_y,
                            page_width - right_margin,
                            page_height - bottom_margin
                        )

                        rc2 = page.insert_textbox(
                            text_rect,
                            translated_text,
                            fontsize=smaller_font,
                            color=overlay_color,
                            fontname="helv",
                            align=fitz.TEXT_ALIGN_LEFT,
                        )

                        # Recalculate lines with smaller font
                        smaller_chars_per_line = int(usable_width / (smaller_font * 0.5))
                        if smaller_chars_per_line > 0:
                            if rc2 >= 0:
                                text_length = len(translated_text)
                            else:
                                text_length = len(translated_text) + rc2
                            lines_used = max(1, text_length // smaller_chars_per_line + 1)

                        current_y += lines_used * (smaller_font * 1.5) + 10

                except Exception as e:
                    print(f"Warning: Failed to insert text on page {page_idx + 1}: {e}", file=sys.stderr)
                    current_y += line_height

        # Save the new PDF
        doc.save(output_pdf_path, garbage=4, deflate=True, clean=True)
        doc.close()

    except Exception as e:
        raise RuntimeError(f"Error rebuilding PDF: {e}")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Rebuild PDF with translated text using PyMuPDF"
    )
    parser.add_argument("original_pdf", help="Path to original PDF file")
    parser.add_argument("extracted_json", help="Path to extracted JSON file")
    parser.add_argument("translated_json", help="Path to translated JSON file")
    parser.add_argument("output_pdf", help="Path to output PDF file")

    args = parser.parse_args()

    try:
        print(f"Rebuilding PDF with translations...")
        print(f"  Original: {args.original_pdf}")
        print(f"  Output: {args.output_pdf}")

        rebuild_pdf_with_translation(
            args.original_pdf,
            args.extracted_json,
            args.translated_json,
            args.output_pdf
        )

        print(f"PDF rebuilt successfully!")
        print(f"  Saved to: {args.output_pdf}")

        return 0

    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    sys.exit(main())
