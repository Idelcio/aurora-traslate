#!/usr/bin/env python3
"""
Compare outlines from two PDFs side by side.
"""

import sys
import fitz  # PyMuPDF

def show_outline(pdf_path, label):
    """Show detailed outline from a PDF."""

    print(f"\n{'='*80}")
    print(f"{label}")
    print(f"{'='*80}")
    print(f"File: {pdf_path}")

    try:
        doc = fitz.open(pdf_path)
        print(f"Total pages: {len(doc)}")

        # Extract outline
        toc = doc.get_toc()

        if toc:
            print(f"\nOutline entries: {len(toc)}\n")

            for i, item in enumerate(toc, 1):
                level = item[0]
                title = item[1]
                page = item[2]

                indent = "  " * (level - 1)
                print(f"{i}. {indent}Level {level}: '{title}' -> page {page}")
        else:
            print("\nNO OUTLINE FOUND")

        doc.close()

    except Exception as e:
        print(f"\nERROR: {e}")

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python compare_outlines.py <original.pdf> <translated.pdf>")
        sys.exit(1)

    original = sys.argv[1]
    translated = sys.argv[2]

    show_outline(original, "ORIGINAL PDF")
    show_outline(translated, "TRANSLATED PDF")

    print("\n" + "="*80)
    print("COMPARISON COMPLETE")
    print("="*80 + "\n")
