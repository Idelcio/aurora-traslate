#!/usr/bin/env python3
"""
Debug script to test Hebrew font rendering in PyMuPDF.
Creates multiple test PDFs with different configurations.
"""

import fitz
import os
import sys

# Sample Hebrew text (Genesis 1:1 and Psalm 23:1)
HEBREW_TEXT = """בְּרֵאשִׁית בָּרָא אֱלֹהִים אֵת הַשָּׁמַיִם וְאֵת הָאָרֶץ׃

יְהוָה רֹעִי לֹא אֶחְסָר׃
בִּנְאוֹת דֶּשֶׁא יַרְבִּיצֵנִי עַל־מֵי מְנֻחוֹת יְנַהֲלֵנִי׃"""


def test_font(font_path, font_name, output_path, use_textbox=True):
    """Test a specific font configuration."""
    print(f"\n{'='*60}")
    print(f"Testing: {font_name}")
    print(f"Font path: {font_path}")
    print(f"Method: {'textbox' if use_textbox else 'insert_text'}")
    print(f"Output: {output_path}")
    print(f"{'='*60}")

    # Check if font file exists
    if not os.path.exists(font_path):
        print(f"ERROR: Font file not found: {font_path}")
        return False

    try:
        # Create PDF
        doc = fitz.open()
        page = doc.new_page(width=595, height=842)

        # Page settings
        left_margin = 50
        right_margin = 50
        top_margin = 50
        font_size = 14

        # Add title
        page.insert_text(
            (left_margin, top_margin),
            f"Font Test: {font_name}",
            fontsize=10,
            fontname="helv",
            color=(0, 0, 0)
        )

        current_y = top_margin + 30

        # Test Hebrew text
        lines = HEBREW_TEXT.strip().split('\n')

        for line in lines:
            if not line.strip():
                current_y += font_size * 0.5
                continue

            if use_textbox:
                # Method 1: Using textbox with right alignment (recommended for RTL)
                rect = fitz.Rect(
                    left_margin,
                    current_y - font_size,
                    595 - right_margin,
                    current_y + font_size * 2
                )

                rc = page.insert_textbox(
                    rect,
                    line,
                    fontsize=font_size,
                    fontfile=font_path,
                    align=fitz.TEXT_ALIGN_RIGHT,
                    color=(0, 0, 0)
                )

                if rc < 0:
                    print(f"WARNING: textbox insertion failed with code {rc}")
            else:
                # Method 2: Using insert_text (for comparison)
                page.insert_text(
                    (left_margin, current_y),
                    line,
                    fontsize=font_size,
                    fontfile=font_path,
                    color=(0, 0, 0)
                )

            current_y += font_size * 1.8

        # Add info at bottom
        info_y = 800
        page.insert_text(
            (left_margin, info_y),
            f"Font file: {os.path.basename(font_path)}",
            fontsize=8,
            fontname="helv",
            color=(0.5, 0.5, 0.5)
        )
        page.insert_text(
            (left_margin, info_y + 12),
            f"Method: {'insert_textbox (RTL)' if use_textbox else 'insert_text (LTR)'}",
            fontsize=8,
            fontname="helv",
            color=(0.5, 0.5, 0.5)
        )

        # Save PDF
        doc.save(output_path, garbage=4, deflate=True)
        doc.close()

        print(f"[OK] PDF created successfully: {output_path}")
        return True

    except Exception as e:
        print(f"[ERROR] {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    # Setup paths
    script_dir = os.path.dirname(os.path.abspath(__file__))
    base_path = os.path.dirname(os.path.dirname(script_dir))
    fonts_dir = os.path.join(base_path, 'storage', 'fonts')
    output_dir = os.path.join(base_path, 'storage', 'test_fonts')

    # Create output directory
    os.makedirs(output_dir, exist_ok=True)

    # Test configurations
    tests = [
        {
            'font_path': os.path.join(fonts_dir, 'SBLHebrew-Regular.ttf'),
            'font_name': 'SBL Hebrew',
            'output': os.path.join(output_dir, 'hebrew_sbl_textbox.pdf'),
            'use_textbox': True
        },
        {
            'font_path': os.path.join(fonts_dir, 'SBLHebrew-Regular.ttf'),
            'font_name': 'SBL Hebrew',
            'output': os.path.join(output_dir, 'hebrew_sbl_inserttext.pdf'),
            'use_textbox': False
        },
        {
            'font_path': os.path.join(fonts_dir, 'NotoSansHebrew-Regular.ttf'),
            'font_name': 'Noto Sans Hebrew',
            'output': os.path.join(output_dir, 'hebrew_noto_textbox.pdf'),
            'use_textbox': True
        },
        {
            'font_path': os.path.join(fonts_dir, 'NotoSansHebrew-Regular.ttf'),
            'font_name': 'Noto Sans Hebrew',
            'output': os.path.join(output_dir, 'hebrew_noto_inserttext.pdf'),
            'use_textbox': False
        },
    ]

    print("\n" + "="*60)
    print("Hebrew Font Rendering Test")
    print("="*60)

    results = []
    for test in tests:
        success = test_font(
            test['font_path'],
            test['font_name'],
            test['output'],
            test['use_textbox']
        )
        results.append((test['font_name'], test['output'], success))

    # Summary
    print("\n" + "="*60)
    print("Test Summary")
    print("="*60)
    for name, output, success in results:
        status = "[OK] SUCCESS" if success else "[X] FAILED"
        print(f"{status}: {os.path.basename(output)}")

    print(f"\nAll test PDFs saved in: {output_dir}")
    print("\nPlease open the PDFs and check if Hebrew text is displayed correctly.")
    print("Expected: Hebrew characters should be visible and properly shaped.")
    print("="*60)


if __name__ == "__main__":
    main()
