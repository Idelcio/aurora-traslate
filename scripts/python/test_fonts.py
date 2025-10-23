#!/usr/bin/env python3
"""
Test script to validate Greek and Hebrew fonts in PDF generation.
Creates sample PDFs with Greek and Hebrew text to verify font rendering.
"""

import json
import os
import sys

# Sample Greek text (John 3:16 in Greek)
GREEK_TEXT = """
Οὕτως γὰρ ἠγάπησεν ὁ θεὸς τὸν κόσμον, ὥστε τὸν υἱὸν τὸν μονογενῆ ἔδωκεν,
ἵνα πᾶς ὁ πιστεύων εἰς αὐτὸν μὴ ἀπόληται ἀλλ' ἔχῃ ζωὴν αἰώνιον.

Ἐν ἀρχῇ ἦν ὁ λόγος, καὶ ὁ λόγος ἦν πρὸς τὸν θεόν, καὶ θεὸς ἦν ὁ λόγος.
"""

# Sample Hebrew text (Genesis 1:1 and Psalm 23:1 in Hebrew)
HEBREW_TEXT = """
בְּרֵאשִׁית בָּרָא אֱלֹהִים אֵת הַשָּׁמַיִם וְאֵת הָאָרֶץ׃

יְהוָה רֹעִי לֹא אֶחְסָר׃
בִּנְאוֹת דֶּשֶׁא יַרְבִּיצֵנִי עַל־מֵי מְנֻחוֹת יְנַהֲלֵנִי׃
"""


def create_test_json(language, text, output_path):
    """Create a test JSON file with sample text."""
    data = {
        "pages": [
            {
                "pageNumber": 1,
                "textItems": [
                    {
                        "originalText": text,
                        "translatedText": text
                    }
                ]
            }
        ]
    }

    with open(output_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    print(f"Created test JSON: {output_path}")


def main():
    script_dir = os.path.dirname(os.path.abspath(__file__))
    base_path = os.path.dirname(os.path.dirname(script_dir))
    test_dir = os.path.join(base_path, 'storage', 'test_fonts')

    # Create test directory
    os.makedirs(test_dir, exist_ok=True)

    # Create test files
    greek_json_path = os.path.join(test_dir, 'test_greek.json')
    hebrew_json_path = os.path.join(test_dir, 'test_hebrew.json')

    create_test_json('greek', GREEK_TEXT, greek_json_path)
    create_test_json('hebrew', HEBREW_TEXT, hebrew_json_path)

    # Paths for output PDFs
    greek_pdf_path = os.path.join(test_dir, 'test_greek.pdf')
    hebrew_pdf_path = os.path.join(test_dir, 'test_hebrew.pdf')

    print("\n" + "="*60)
    print("Font Test Files Created Successfully!")
    print("="*60)
    print("\nTo test PDF generation, run:")
    print(f"\n1. Greek PDF:")
    print(f'   python "{os.path.join(script_dir, "create_simple_translated_pdf.py")}" "{greek_json_path}" "{greek_pdf_path}"')
    print(f"\n2. Hebrew PDF:")
    print(f'   python "{os.path.join(script_dir, "create_simple_translated_pdf.py")}" "{hebrew_json_path}" "{hebrew_pdf_path}"')
    print("\n" + "="*60)


if __name__ == "__main__":
    main()
