#!/usr/bin/env python3
"""
Create a SIMPLE PDF with just translated text.
No fancy layout - just clean, readable translated content.
Supports Greek (Gentium Plus/Noto Serif Greek) and Hebrew (SBL Hebrew/Noto Sans Hebrew) fonts.
"""

import argparse
import json
import sys
import textwrap
import os
import re

try:
    import fitz  # PyMuPDF
except ImportError:
    print("Error: PyMuPDF not installed")
    sys.exit(1)


def load_json(path):
    with open(path, 'r', encoding='utf-8') as f:
        return json.load(f)


def detect_language(text):
    """Detect if text contains Greek or Hebrew characters."""
    # Greek Unicode ranges: 0370-03FF (Greek and Coptic), 1F00-1FFF (Greek Extended)
    greek_pattern = re.compile(r'[\u0370-\u03FF\u1F00-\u1FFF]')
    # Hebrew Unicode range: 0590-05FF
    hebrew_pattern = re.compile(r'[\u0590-\u05FF]')

    if greek_pattern.search(text):
        return 'greek'
    elif hebrew_pattern.search(text):
        return 'hebrew'
    else:
        return 'default'


def get_font_config(language, base_path):
    """
    Get font configuration based on detected language.

    Returns: tuple (font_path, font_name, requires_rtl)
    - font_path: Path to the TTF file (or None for built-in fonts)
    - font_name: Font name to use in PyMuPDF
    - requires_rtl: Boolean indicating if text should be rendered right-to-left
    """
    fonts_dir = os.path.join(base_path, 'storage', 'fonts')

    if language == 'greek':
        # Prefer Gentium Plus, fallback to Noto Serif Greek
        gentium_path = os.path.join(fonts_dir, 'GentiumPlus-Regular.ttf')
        noto_greek_path = os.path.join(fonts_dir, 'NotoSerifGreek-Regular.ttf')

        if os.path.exists(gentium_path):
            return (gentium_path, 'GentiumPlus', False)
        elif os.path.exists(noto_greek_path):
            return (noto_greek_path, 'NotoSerifGreek', False)
        else:
            print("Warning: Greek fonts not found, using default font", file=sys.stderr)
            return (None, 'helv', False)

    elif language == 'hebrew':
        # Prefer SBL Hebrew, fallback to Noto Sans Hebrew
        sbl_path = os.path.join(fonts_dir, 'SBLHebrew-Regular.ttf')
        noto_hebrew_path = os.path.join(fonts_dir, 'NotoSansHebrew-Regular.ttf')

        if os.path.exists(sbl_path):
            return (sbl_path, 'SBLHebrew', True)
        elif os.path.exists(noto_hebrew_path):
            return (noto_hebrew_path, 'NotoSansHebrew', True)
        else:
            print("Warning: Hebrew fonts not found, using default font", file=sys.stderr)
            return (None, 'helv', True)

    else:
        # Default font (built-in Helvetica)
        return (None, 'helv', False)


def create_simple_pdf(translated_json_path, output_pdf_path):
    """Create a simple, clean PDF with translated text only."""

    # Load translation data
    translated = load_json(translated_json_path)

    # Detect base path (assuming script is in scripts/python/)
    script_dir = os.path.dirname(os.path.abspath(__file__))
    base_path = os.path.dirname(os.path.dirname(script_dir))

    # Sample some text to detect language
    sample_text = ""
    for page_data in translated.get("pages", [])[:3]:  # Check first 3 pages
        for item in page_data.get("textItems", [])[:5]:  # Check first 5 items per page
            sample_text += item.get("translatedText", "")
            if len(sample_text) > 500:  # 500 chars should be enough
                break
        if len(sample_text) > 500:
            break

    # Detect language and get font configuration
    language = detect_language(sample_text)
    font_path, font_name, requires_rtl = get_font_config(language, base_path)

    print(f"Detected language: {language}", file=sys.stderr)
    print(f"Using font: {font_name}", file=sys.stderr)
    if font_path:
        print(f"Font file: {font_path}", file=sys.stderr)

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

                # For RTL languages (Hebrew), use textbox for proper rendering
                text_to_insert = line

                # Insert line of text
                try:
                    if requires_rtl:
                        # For RTL languages (Hebrew), use textbox with right alignment
                        rect = fitz.Rect(
                            left_margin,
                            current_y - font_size,
                            page_width - right_margin,
                            current_y + line_height
                        )

                        if font_path and os.path.exists(font_path):
                            rc = page.insert_textbox(
                                rect,
                                text_to_insert,
                                fontsize=font_size,
                                fontfile=font_path,
                                align=fitz.TEXT_ALIGN_RIGHT,
                                color=(0, 0, 0),
                            )
                            if rc < 0:
                                print(f"Warning: textbox insertion failed with code {rc}", file=sys.stderr)
                        else:
                            page.insert_textbox(
                                rect,
                                text_to_insert,
                                fontsize=font_size,
                                fontname=font_name,
                                align=fitz.TEXT_ALIGN_RIGHT,
                                color=(0, 0, 0),
                            )
                    else:
                        # For LTR languages (Greek, English, etc.)
                        if font_path and os.path.exists(font_path):
                            page.insert_text(
                                (left_margin, current_y),
                                text_to_insert,
                                fontsize=font_size,
                                fontfile=font_path,
                                set_simple=0,  # 0 = embed font subset
                                color=(0, 0, 0),
                            )
                        else:
                            page.insert_text(
                                (left_margin, current_y),
                                text_to_insert,
                                fontsize=font_size,
                                fontname=font_name,
                                color=(0, 0, 0),
                            )
                except Exception as e:
                    print(f"Warning: Failed to insert line: {e}", file=sys.stderr)
                    print(f"Line content: {text_to_insert[:50]}...", file=sys.stderr)
                    # Try fallback with built-in font
                    try:
                        page.insert_text(
                            (left_margin, current_y),
                            text_to_insert,
                            fontsize=font_size,
                            fontname="helv",
                            color=(0, 0, 0),
                        )
                    except Exception as e2:
                        print(f"Fallback also failed: {e2}", file=sys.stderr)

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
