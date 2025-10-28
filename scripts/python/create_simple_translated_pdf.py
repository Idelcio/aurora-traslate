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
    font_size = 12  # Increased from 11 for better readability
    line_height = font_size * 1.8  # Increased spacing for better readability
    paragraph_spacing = line_height * 0.8  # Space between paragraphs

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
            # Approximate: 65 characters per line for 12pt font with good margins
            wrapped_lines = textwrap.wrap(
                paragraph,
                width=75,  # Adjusted for better line length
                break_long_words=False,
                break_on_hyphens=True
            )

            for line_idx, line in enumerate(wrapped_lines):
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

            # Add paragraph spacing after each paragraph
            current_y += paragraph_spacing

    # Add outline (table of contents) if available
    outline = translated.get("outline", [])
    if outline:
        print(f"Adding outline with {len(outline)} entries", file=sys.stderr)

        # Build TOC in PyMuPDF format: list of [level, title, page_number]
        toc = []
        for item in outline:
            level = item.get("level", 1)
            title = item.get("title", "")
            page = item.get("page", 1)

            # Ensure page number is within bounds
            if page > len(doc):
                page = len(doc)
            if page < 1:
                page = 1

            toc.append([level, title, page])

        try:
            doc.set_toc(toc)
            print(f"Successfully added outline", file=sys.stderr)
        except Exception as e:
            print(f"Warning: Could not add outline: {e}", file=sys.stderr)
    else:
        print("No outline to add", file=sys.stderr)

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
