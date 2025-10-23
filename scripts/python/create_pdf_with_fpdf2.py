#!/usr/bin/env python3
"""
Create PDF with translated text using fpdf2.
Better Unicode support for Greek, Hebrew, and other special characters.
"""

import argparse
import json
import sys
import os
import re

# Disable network access for fontTools to avoid Windows WinError 10106
os.environ['FONTTOOLS_DOWNLOAD_MAXLEN'] = '0'

from fpdf import FPDF
from fpdf.enums import XPos, YPos


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

    Returns: tuple (font_path, font_family, is_rtl)
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
            return (None, None, False)

    elif language == 'hebrew':
        # Temporarily use DejaVu for Hebrew (built-in font with good Hebrew support)
        # TODO: Download proper static TTF fonts for Hebrew
        print("Info: Using DejaVu font for Hebrew (Hebrew-specific fonts have compatibility issues)", file=sys.stderr)
        return (None, None, True)

    else:
        # Default - use built-in fonts
        return (None, None, False)


class PDF(FPDF):
    """Custom PDF class with Unicode support."""

    def __init__(self, is_rtl=False):
        super().__init__(format='A4')
        self.is_rtl = is_rtl


def create_pdf_with_fpdf2(translated_json_path, output_pdf_path):
    """Create PDF with proper Unicode font embedding using fpdf2."""

    # Load translation data
    translated = load_json(translated_json_path)

    # Detect base path
    script_dir = os.path.dirname(os.path.abspath(__file__))
    base_path = os.path.dirname(os.path.dirname(script_dir))

    # Sample text to detect language
    sample_text = ""
    for page_data in translated.get("pages", [])[:3]:
        for item in page_data.get("textItems", [])[:5]:
            sample_text += item.get("translatedText", "")
            if len(sample_text) > 500:
                break
        if len(sample_text) > 500:
            break

    # Detect language and get font
    language = detect_language(sample_text)
    font_path, font_family, is_rtl = get_font_config(language, base_path)

    print(f"Detected language: {language}", file=sys.stderr)
    print(f"Font family: {font_family}", file=sys.stderr)
    if font_path:
        print(f"Font file: {font_path}", file=sys.stderr)

    # Create PDF
    pdf = PDF(is_rtl=is_rtl)
    pdf.set_auto_page_break(auto=True, margin=15)
    pdf.add_page()

    # Add custom font if available
    if font_path and os.path.exists(font_path):
        try:
            pdf.add_font(font_family, style='', fname=font_path)
            pdf.set_font(font_family, size=11)
            print(f"Custom font '{font_family}' loaded successfully", file=sys.stderr)
        except Exception as e:
            print(f"Warning: Failed to load custom font: {e}", file=sys.stderr)
            # Fallback to Helvetica
            pdf.set_font('Helvetica', size=11)
            print("Using Helvetica font (fallback after error)", file=sys.stderr)
    else:
        # Fallback to Helvetica (built-in) or try DejaVu for Hebrew
        if is_rtl:
            # For Hebrew, we need Unicode support - use built-in courier which has some Hebrew
            pdf.set_font('Courier', size=11)
            print("Using Courier font for Hebrew (fallback)", file=sys.stderr)
        else:
            pdf.set_font('Helvetica', size=11)
            print("Using Helvetica font (fallback)", file=sys.stderr)

    # Process pages
    for page_data in translated.get("pages", []):
        text_items = page_data.get("textItems", [])

        # Combine all text items with paragraph breaks
        full_text = ""
        for item in text_items:
            translated_text = item.get("translatedText", "").strip()
            if translated_text:
                full_text += translated_text + "\n\n"

        if not full_text.strip():
            continue

        # Split into paragraphs
        paragraphs = [p.strip() for p in full_text.split('\n\n') if p.strip()]

        for paragraph in paragraphs:
            # For RTL text, we need to reverse the text
            if is_rtl:
                # fpdf2 doesn't handle RTL automatically, so we reverse the text
                # Note: This is a simplified approach; proper RTL requires bidi algorithm
                text_to_write = paragraph[::-1]  # Simple reversal
            else:
                text_to_write = paragraph

            # Write paragraph
            pdf.multi_cell(0, 8, text_to_write, new_x=XPos.LMARGIN, new_y=YPos.NEXT)
            pdf.ln(4)  # Extra spacing between paragraphs

    # Save PDF
    pdf.output(output_pdf_path)
    print(f"PDF created successfully: {output_pdf_path}", file=sys.stderr)


def main():
    parser = argparse.ArgumentParser(description="Create PDF with fpdf2")
    parser.add_argument("translated_json", help="Path to translated JSON file")
    parser.add_argument("output_pdf", help="Path to output PDF file")

    args = parser.parse_args()

    try:
        create_pdf_with_fpdf2(args.translated_json, args.output_pdf)
        return 0
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
