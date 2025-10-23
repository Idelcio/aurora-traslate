# PDF Translation with OCR Support

## Overview

This project now supports **OCR (Optical Character Recognition)** for scanned/image-based PDFs using Tesseract.

## Features

- ✅ Automatic OCR for scanned PDFs
- ✅ Parallel processing (4 workers by default)
- ✅ Supports Portuguese and English
- ✅ Handles 135+ page documents
- ✅ ~2.5 seconds per page

## Requirements

### Python Packages
```bash
pip install PyMuPDF pytesseract Pillow requests
```

### Tesseract OCR
**Windows:** Download from https://github.com/UB-Mannheim/tesseract/wiki

**Verify installation:**
```bash
tesseract --version
```

## Scripts

### 1. `extract_pdf_with_ocr_always.py` (Recommended)
Always uses OCR - best for scanned PDFs.

```bash
python extract_pdf_with_ocr_always.py input.pdf output.json [zoom] [workers]
```

**Parameters:**
- `zoom`: Image resolution multiplier (default: 2.0)
- `workers`: Parallel OCR workers (default: 4)

**Performance:**
- 135 pages: ~5-6 minutes
- 10 pages: ~25 seconds

### 2. `extract_pdf_text_with_ocr.py`
Hybrid approach - tries normal extraction first, OCR as fallback.

```bash
python extract_pdf_text_with_ocr.py input.pdf output.json [--no-ocr]
```

### 3. `extract_pdf_text.py`
Regular extraction without OCR (fastest, but doesn't work for scanned PDFs).

## Full Pipeline

```
1. PDF Upload
   ↓
2. Python OCR Extraction (extract_pdf_with_ocr_always.py)
   → Converts each page to image
   → Runs Tesseract OCR
   → Saves to JSON
   ↓
3. PHP Translation (GoogleTranslateService)
   → Calls Google Translate API
   → Deduplicates texts
   → Batch processing
   ↓
4. Python PDF Rebuild (create_simple_translated_pdf.py)
   → Creates new PDF with translated text
   ↓
5. Download Translated PDF
```

## Troubleshooting

### OCR not working
```bash
# Check if Tesseract is installed
tesseract --version

# Install Tesseract if missing (Windows)
# Download from: https://github.com/UB-Mannheim/tesseract/wiki

# Install Python packages
pip install pytesseract Pillow
```

### Slow performance
- Reduce `zoom` parameter (1.5 instead of 2.0)
- Increase `workers` parameter (6-8 on powerful CPUs)

### Poor OCR quality
- Increase `zoom` parameter (2.5 or 3.0)
- Check if PDF is very low resolution

## Performance Benchmarks

| Pages | Time    | Pages/sec |
|-------|---------|-----------|
| 10    | 24.7s   | 0.4       |
| 50    | ~2min   | 0.4       |
| 135   | ~5-6min | 0.4       |

**Note:** OCR is CPU-intensive. Times may vary based on:
- CPU performance
- Image quality
- Text density
