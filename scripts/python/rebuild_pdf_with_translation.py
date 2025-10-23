#!/usr/bin/env python3
"""
Rebuild PDF with translated text overlays using PyMuPDF.
Python replacement for rebuildPdfWithTranslation.cjs with better performance.
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


def load_json(path: str) -> Dict:
    """Load JSON file."""
    with open(path, "r", encoding="utf-8") as f:
        return json.load(f)


def rebuild_pdf_with_translation(
    original_pdf_path: str,
    extracted_json_path: str,
    translated_json_path: str,
    output_pdf_path: str,
    overlay_color: tuple = (0, 0, 0),  # Black
    background_color: tuple = (1, 1, 1),  # White
) -> None:
    """
    Create a new PDF with translated text overlaid on the original.
    
    Strategy:
    1. Open original PDF
    2. For each text item, draw a white rectangle over it
    3. Write the translated text in the same position
    """
    try:
        # Load translation data
        extracted = load_json(extracted_json_path)
        translated = load_json(translated_json_path)
        
        # Open original PDF
        doc = fitz.open(original_pdf_path)
        
        # Process each page
        for page_idx, page_data in enumerate(translated.get("pages", [])):
            if page_idx >= len(doc):
                break
            
            page = doc[page_idx]
            
            # Get translated text items
            text_items = page_data.get("textItems", [])
            
            for item in text_items:
                original_text = item.get("originalText", "")
                translated_text = item.get("translatedText", "")
                
                if not translated_text or translated_text == original_text:
                    continue
                
                # Find corresponding item in extracted data to get position
                extracted_page = extracted["pages"][page_idx]
                position_item = None
                
                for ext_item in extracted_page.get("textItems", []):
                    if ext_item.get("text") == original_text:
                        position_item = ext_item
                        break
                
                if not position_item:
                    continue
                
                # Get position and size
                x = position_item.get("x", 0)
                y = position_item.get("y", 0)
                width = position_item.get("width", 100)
                height = position_item.get("height", 12)
                font_size = position_item.get("fontSize", 10)

                # FIRST: Add large white rectangle to cover ALL original text
                # Make it much larger to ensure coverage
                cover_rect = fitz.Rect(x - 5, y - height - 5, x + width + 200, y + 10)
                shape = page.new_shape()
                shape.draw_rect(cover_rect)
                shape.finish(fill=background_color, color=None)
                shape.commit()

                # SECOND: Insert translated text on top
                try:
                    rc = page.insert_text(
                        (x, y),
                        translated_text,
                        fontsize=font_size,
                        color=overlay_color,
                        fontname="helv",
                    )
                    if rc < 0:
                        # Try again with smaller font if it failed
                        page.insert_text(
                            (x, y),
                            translated_text,
                            fontsize=font_size * 0.9,
                            color=overlay_color,
                            fontname="helv",
                        )
                except Exception as e:
                    # Fallback - just insert without checking
                    page.insert_text(
                        (x, y),
                        translated_text,
                        fontsize=font_size,
                        color=overlay_color,
                    )
        
        # Save the modified PDF
        doc.save(output_pdf_path, garbage=4, deflate=True, clean=True)
        doc.close()
        
    except Exception as e:
        raise RuntimeError(f"Error rebuilding PDF: {e}")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Rebuild PDF with translated text using PyMuPDF"
    )
    parser.add_argument("original_pdf", help="Path to original PDF file")
    parser.add_argument("extracted_json", help="Path to extracted JSON file")
    parser.add_argument("translated_json", help="Path to translated JSON file")
    parser.add_argument("output_pdf", help="Path to output PDF file")
    
    args = parser.parse_args()
    
    try:
        print(f"Rebuilding PDF with translations...")
        print(f"  Original: {args.original_pdf}")
        print(f"  Output: {args.output_pdf}")
        
        rebuild_pdf_with_translation(
            args.original_pdf,
            args.extracted_json,
            args.translated_json,
            args.output_pdf
        )
        
        print(f"PDF rebuilt successfully!")
        print(f"  Saved to: {args.output_pdf}")
        
        return 0
        
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    sys.exit(main())
