#!/usr/bin/env python3
"""
Extract text and formatting metadata from PDF files using PyMuPDF (fitz).
This is a Python replacement for extractPdfText.cjs with better performance.
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


def extract_pdf_data(pdf_path: str) -> Dict:
    """
    Extract text and metadata from a PDF file.
    
    Returns structure compatible with the Node.js version but with better performance.
    """
    try:
        doc = fitz.open(pdf_path)
        
        pdf_data = {
            "numPages": len(doc),
            "pages": []
        }
        
        # Process each page
        for page_num in range(len(doc)):
            page = doc[page_num]
            
            # Get page dimensions
            rect = page.rect
            
            page_data = {
                "pageNumber": page_num + 1,  # 1-indexed like Node.js version
                "width": rect.width,
                "height": rect.height,
                "textItems": []
            }
            
            # Extract text with detailed positioning
            blocks = page.get_text("dict")["blocks"]
            
            for block in blocks:
                if block.get("type") == 0:  # Text block
                    for line in block.get("lines", []):
                        for span in line.get("spans", []):
                            text = span.get("text", "").strip()
                            if text:
                                # Extract detailed formatting info
                                bbox = span.get("bbox", [0, 0, 0, 0])
                                
                                text_item = {
                                    "text": text,
                                    "x": bbox[0],
                                    "y": bbox[1],
                                    "width": bbox[2] - bbox[0],
                                    "height": bbox[3] - bbox[1],
                                    "fontName": span.get("font", "unknown"),
                                    "fontSize": round(span.get("size", 0), 2),
                                    "color": span.get("color", 0),
                                    "flags": span.get("flags", 0),  # bold, italic, etc
                                }
                                
                                page_data["textItems"].append(text_item)
            
            pdf_data["pages"].append(page_data)
        
        doc.close()
        return pdf_data
        
    except Exception as e:
        raise RuntimeError(f"Error extracting PDF data: {e}")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Extract text and formatting from PDF using PyMuPDF"
    )
    parser.add_argument("pdf_path", help="Path to input PDF file")
    parser.add_argument("output_json", help="Path to output JSON file")
    parser.add_argument("--pretty", action="store_true", help="Pretty print JSON")
    
    args = parser.parse_args()
    
    try:
        print(f"Extracting text from: {args.pdf_path}")
        pdf_data = extract_pdf_data(args.pdf_path)
        
        # Save to JSON
        indent = 2 if args.pretty else None
        with open(args.output_json, "w", encoding="utf-8") as f:
            json.dump(pdf_data, f, ensure_ascii=False, indent=indent)
        
        total_items = sum(len(page["textItems"]) for page in pdf_data["pages"])
        
        print("Data extracted successfully!")
        print(f"  Pages: {pdf_data['numPages']}")
        print(f"  Total text items: {total_items}")
        print(f"  Saved to: {args.output_json}")
        
        return 0
        
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    sys.exit(main())
