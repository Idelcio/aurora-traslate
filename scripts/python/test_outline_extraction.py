#!/usr/bin/env python3
"""
Test script to verify outline extraction from PDF.
"""

import sys
import fitz  # PyMuPDF

def test_outline_extraction(pdf_path):
    """Test extracting outline from a PDF."""

    print(f"Opening PDF: {pdf_path}")
    doc = fitz.open(pdf_path)

    print(f"Total pages: {len(doc)}")

    # Extract outline
    toc = doc.get_toc()

    if toc:
        print(f"\nFound {len(toc)} outline entries:")
        print("-" * 80)

        for item in toc:
            level = item[0]
            title = item[1]
            page = item[2]

            indent = "  " * (level - 1)
            print(f"{indent}[{level}] {title} (page {page})")
    else:
        print("\nNo outline found in this PDF")

    doc.close()

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python test_outline_extraction.py <pdf_file>")
        sys.exit(1)

    pdf_path = sys.argv[1]
    test_outline_extraction(pdf_path)
