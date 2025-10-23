#!/usr/bin/env python3
"""
Extract text from PDF with OCR fallback for scanned documents.
Uses PyMuPDF for text extraction and pytesseract for OCR when needed.
"""

import sys
import json
import fitz  # PyMuPDF
from pathlib import Path
import platform

try:
    from PIL import Image
    import pytesseract
    import io
    HAS_OCR = True

    # Configure Tesseract path for Windows
    if platform.system() == 'Windows':
        import os
        possible_paths = [
            r'C:\Program Files\Tesseract-OCR\tesseract.exe',
            r'C:\Program Files (x86)\Tesseract-OCR\tesseract.exe',
            r'C:\Tesseract-OCR\tesseract.exe',
        ]
        for path in possible_paths:
            if os.path.exists(path):
                pytesseract.pytesseract.tesseract_cmd = path
                break

except ImportError:
    HAS_OCR = False
    print("WARNING: OCR libraries not available. Install with: pip install pytesseract pillow", file=sys.stderr)


def extract_text_from_page(page, use_ocr=False):
    """Extract text from a page, using OCR if needed."""

    # Try normal text extraction first - use simple text() for better compatibility
    simple_text = page.get_text().strip()

    # Also try dict method for detailed positioning
    text_dict = page.get_text("dict")
    text_items = []

    for block in text_dict.get("blocks", []):
        if block.get("type") == 0:  # Text block
            for line in block.get("lines", []):
                for span in line.get("spans", []):
                    text = span.get("text", "").strip()
                    if text:
                        text_items.append({
                            "text": text,
                            "x": span.get("bbox", [0])[0],
                            "y": span.get("bbox", [0])[1],
                            "width": span.get("bbox", [2])[0] - span.get("bbox", [0])[0],
                            "height": span.get("bbox", [3])[1] - span.get("bbox", [1])[1],
                            "fontName": span.get("font", ""),
                            "fontSize": span.get("size", 0),
                            "color": span.get("color", 0),
                            "flags": span.get("flags", 0),
                        })

    # If dict method found nothing but simple text found something, add it
    if not text_items and simple_text:
        print(f"  Using simple text extraction (dict method found nothing)", file=sys.stderr)
        text_items.append({
            "text": simple_text,
            "x": 0,
            "y": 0,
            "width": page.rect.width,
            "height": page.rect.height,
            "fontName": "Unknown",
            "fontSize": 12,
            "color": 0,
            "flags": 0,
            "extractionMethod": "simple",
        })

    # If still no text found and OCR is available, try OCR
    if not text_items and use_ocr and HAS_OCR:
        print(f"  No text found, attempting OCR...", file=sys.stderr)

        # Render page as image
        mat = fitz.Matrix(2, 2)  # 2x zoom for better OCR
        pix = page.get_pixmap(matrix=mat)

        # Convert to PIL Image
        img_data = pix.tobytes("png")
        img = Image.open(io.BytesIO(img_data))

        # Perform OCR
        ocr_text = pytesseract.image_to_string(img, lang='por+eng')

        if ocr_text.strip():
            # Add OCR text as single item
            text_items.append({
                "text": ocr_text.strip(),
                "x": 0,
                "y": 0,
                "width": page.rect.width,
                "height": page.rect.height,
                "fontName": "OCR",
                "fontSize": 12,
                "color": 0,
                "flags": 0,
                "isOCR": True,
            })
            print(f"  OCR extracted {len(ocr_text)} characters", file=sys.stderr)

    return text_items


def extract_pdf_text(pdf_path, output_json_path, use_ocr=True):
    """Extract text and metadata from PDF."""

    print(f"Extracting text from: {pdf_path}", file=sys.stderr)

    if use_ocr and not HAS_OCR:
        print("WARNING: OCR requested but libraries not installed", file=sys.stderr)
        use_ocr = False

    doc = fitz.open(pdf_path)

    result = {
        "numPages": len(doc),
        "pages": []
    }

    for page_num in range(len(doc)):
        page = doc[page_num]

        text_items = extract_text_from_page(page, use_ocr=use_ocr)

        result["pages"].append({
            "pageNumber": page_num + 1,
            "width": page.rect.width,
            "height": page.rect.height,
            "textItems": text_items
        })

        print(f"  Page {page_num + 1}/{len(doc)}: {len(text_items)} text items", file=sys.stderr)

    # Save to JSON
    with open(output_json_path, 'w', encoding='utf-8') as f:
        json.dump(result, f, ensure_ascii=False, indent=2)

    print(f"\nData extracted successfully!", file=sys.stderr)
    print(f"  Pages: {len(doc)}", file=sys.stderr)
    print(f"  Total text items: {sum(len(p['textItems']) for p in result['pages'])}", file=sys.stderr)
    print(f"  Saved to: {output_json_path}", file=sys.stderr)


if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python extract_pdf_text_with_ocr.py <input.pdf> <output.json> [--no-ocr]", file=sys.stderr)
        sys.exit(1)

    pdf_path = sys.argv[1]
    output_json = sys.argv[2]
    use_ocr = "--no-ocr" not in sys.argv

    try:
        extract_pdf_text(pdf_path, output_json, use_ocr=use_ocr)
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        sys.exit(1)
