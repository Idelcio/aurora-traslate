#!/usr/bin/env python3
"""
Simple PDF rebuild - creates new PDF with translated text.
Uses reportlab for better text control.
"""
import argparse
import json
import sys

try:
    import fitz
    from reportlab.pdfgen import canvas
    from reportlab.lib.pagesizes import letter
    from reportlab.pdfbase import pdfmetrics
    from reportlab.pdfbase.ttfonts import TTFont
except ImportError:
    print("Missing dependencies. Run: pip install PyMuPDF reportlab")
    sys.exit(1)


def load_json(path):
    with open(path, 'r', encoding='utf-8') as f:
        return json.load(f)


def rebuild_pdf(original_pdf, extracted_json, translated_json, output_pdf):
    """Create new PDF with translated text overlaid on original."""
    
    # Load data
    extracted = load_json(extracted_json)
    translated = load_json(translated_json)
    
    # Open original PDF to get as background
    original_doc = fitz.open(original_pdf)
    
    # Get page size from first page
    first_page = original_doc[0]
    page_rect = first_page.rect
    page_width = page_rect.width
    page_height = page_rect.height
    
    # Create new PDF with reportlab
    c = canvas.Canvas(output_pdf, pagesize=(page_width, page_height))
    
    for page_idx, page_data in enumerate(translated.get("pages", [])):
        if page_idx >= len(original_doc):
            break
            
        # Render original page as image background
        orig_page = original_doc[page_idx]
        pix = orig_page.get_pixmap(matrix=fitz.Matrix(2, 2))  # 2x resolution
        img_path = f"/tmp/page_{page_idx}.png"
        pix.save(img_path)
        
        # Draw original as background
        c.drawImage(img_path, 0, 0, width=page_width, height=page_height)
        
        # Overlay translated texts
        text_items = page_data.get("textItems", [])
        extracted_page = extracted["pages"][page_idx]
        
        for item in text_items:
            original_text = item.get("originalText", "")
            translated_text = item.get("translatedText", "")
            
            if not translated_text or translated_text == original_text:
                continue
                
            # Find position
            position_item = None
            for ext_item in extracted_page.get("textItems", []):
                if ext_item.get("text") == original_text:
                    position_item = ext_item
                    break
                    
            if not position_item:
                continue
                
            x = position_item.get("x", 0)
            y = position_item.get("y", 0)
            font_size = position_item.get("fontSize", 10)
            
            # reportlab uses bottom-left origin, PDF uses top-left
            y_reportlab = page_height - y
            
            # Draw white rectangle to cover original
            c.setFillColorRGB(1, 1, 1)  # white
            c.rect(x, y_reportlab - font_size, 200, font_size + 5, fill=1, stroke=0)
            
            # Draw translated text
            c.setFillColorRGB(0, 0, 0)  # black
            c.setFont("Helvetica", font_size)
            c.drawString(x, y_reportlab - font_size, translated_text)
        
        c.showPage()
    
    c.save()
    original_doc.close()
    print(f"PDF rebuilt: {output_pdf}")


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("original_pdf")
    parser.add_argument("extracted_json")
    parser.add_argument("translated_json")
    parser.add_argument("output_pdf")
    args = parser.parse_args()
    
    rebuild_pdf(args.original_pdf, args.extracted_json, args.translated_json, args.output_pdf)
