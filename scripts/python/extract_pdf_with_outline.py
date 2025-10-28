#!/usr/bin/env python3
"""
Extract text and outline (table of contents) from PDF.
Based on extract_pdf_with_ocr_always.py but also extracts PDF outline/bookmarks.
"""

import sys
import json
import fitz  # PyMuPDF
from PIL import Image
import pytesseract
import io
from concurrent.futures import ThreadPoolExecutor, as_completed
import time
import platform

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


def extract_outline(doc):
    """
    Extract PDF outline (table of contents / bookmarks).
    Returns a list of outline items with title, page number, and level.
    """
    outline_items = []

    try:
        toc = doc.get_toc()  # Returns list of [level, title, page]

        if toc:
            print(f"Found {len(toc)} outline entries", file=sys.stderr)

            for item in toc:
                level = item[0]  # Indentation level (1 = top level, 2 = sub-item, etc.)
                title = item[1]  # Title text
                page = item[2]   # Page number (1-indexed)

                outline_items.append({
                    'level': level,
                    'title': title,
                    'page': page,
                })
        else:
            print("No outline found in PDF", file=sys.stderr)

    except Exception as e:
        print(f"Warning: Could not extract outline: {e}", file=sys.stderr)

    return outline_items


def ocr_page(args):
    """OCR a single page. Returns (page_num, text, error)."""
    page_num, pdf_path, zoom = args

    try:
        doc = fitz.open(pdf_path)
        page = doc[page_num]

        # Render page as high-res image
        mat = fitz.Matrix(zoom, zoom)
        pix = page.get_pixmap(matrix=mat, alpha=False)

        # Convert to PIL Image
        img_data = pix.tobytes("png")
        img = Image.open(io.BytesIO(img_data))

        # Perform OCR
        ocr_text = pytesseract.image_to_string(img, lang='por+eng')

        doc.close()

        return (page_num, ocr_text.strip(), None)

    except Exception as e:
        return (page_num, "", str(e))


def extract_pdf_with_ocr(pdf_path, output_json_path, zoom=2.0, max_workers=4, max_pages=None):
    """Extract all pages using OCR with parallel processing, plus outline."""

    print(f"Extracting text and outline from: {pdf_path}", file=sys.stderr)
    print(f"Using OCR with {max_workers} workers, zoom={zoom}x", file=sys.stderr)

    start_time = time.time()

    # Get page count and extract outline
    doc = fitz.open(pdf_path)
    total_pages = len(doc)
    page_width = doc[0].rect.width
    page_height = doc[0].rect.height

    # Extract outline before closing
    outline = extract_outline(doc)

    doc.close()

    # Limit pages if requested
    num_pages = min(total_pages, max_pages) if max_pages else total_pages

    if max_pages and num_pages < total_pages:
        print(f"Limiting to first {num_pages} pages (out of {total_pages})", file=sys.stderr)
    else:
        print(f"Total pages: {num_pages}", file=sys.stderr)

    # Prepare tasks
    tasks = [(i, pdf_path, zoom) for i in range(num_pages)]

    # Process pages in parallel
    results = {}
    errors = []

    with ThreadPoolExecutor(max_workers=max_workers) as executor:
        futures = {executor.submit(ocr_page, task): task[0] for task in tasks}

        for future in as_completed(futures):
            page_num, text, error = future.result()

            if error:
                errors.append(f"Page {page_num + 1}: {error}")
                print(f"  Page {page_num + 1}/{num_pages}: ERROR - {error}", file=sys.stderr)
            else:
                results[page_num] = text
                chars = len(text)
                print(f"  Page {page_num + 1}/{num_pages}: {chars} characters", file=sys.stderr)

    # Build output
    output = {
        "numPages": num_pages,
        "outline": outline,  # Add outline to output
        "pages": []
    }

    for page_num in range(num_pages):
        text = results.get(page_num, "")

        output["pages"].append({
            "pageNumber": page_num + 1,
            "width": page_width,
            "height": page_height,
            "textItems": [{
                "text": text,
                "x": 0,
                "y": 0,
                "width": page_width,
                "height": page_height,
                "fontName": "OCR",
                "fontSize": 12,
                "color": 0,
                "flags": 0,
                "isOCR": True,
            }] if text else []
        })

    # Save
    with open(output_json_path, 'w', encoding='utf-8') as f:
        json.dump(output, f, ensure_ascii=False, indent=2)

    elapsed = time.time() - start_time
    total_chars = sum(len(results.get(i, "")) for i in range(num_pages))

    print(f"\nExtraction completed!", file=sys.stderr)
    print(f"  Time: {elapsed:.1f}s ({num_pages / elapsed:.1f} pages/sec)", file=sys.stderr)
    print(f"  Total characters: {total_chars:,}", file=sys.stderr)
    print(f"  Outline entries: {len(outline)}", file=sys.stderr)
    print(f"  Saved to: {output_json_path}", file=sys.stderr)

    if errors:
        print(f"\nErrors: {len(errors)}", file=sys.stderr)
        for err in errors[:5]:
            print(f"  {err}", file=sys.stderr)


if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python extract_pdf_with_outline.py <input.pdf> <output.json> [zoom] [workers] [max_pages]", file=sys.stderr)
        sys.exit(1)

    pdf_path = sys.argv[1]
    output_json = sys.argv[2]
    zoom = float(sys.argv[3]) if len(sys.argv) > 3 else 2.0
    max_workers = int(sys.argv[4]) if len(sys.argv) > 4 else 4
    max_pages = int(sys.argv[5]) if len(sys.argv) > 5 else None

    try:
        extract_pdf_with_ocr(pdf_path, output_json, zoom=zoom, max_workers=max_workers, max_pages=max_pages)
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        sys.exit(1)
