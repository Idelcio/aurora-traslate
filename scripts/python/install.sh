#!/bin/bash
# Quick installation script for Python PDF Translator

echo "================================================"
echo "  Python PDF Translator - Quick Setup"
echo "================================================"
echo ""

# Check Python
if ! command -v python &> /dev/null && ! command -v python3 &> /dev/null; then
    echo "❌ Python not found. Please install Python 3.8+ first."
    exit 1
fi

PYTHON_CMD=$(command -v python3 || command -v python)
echo "✓ Python found: $PYTHON_CMD"
$PYTHON_CMD --version

# Install dependencies
echo ""
echo "Installing Python dependencies..."
$PYTHON_CMD -m pip install -r requirements.txt

# Verify installations
echo ""
echo "Verifying installations..."
$PYTHON_CMD -c "import aiohttp; print('✓ aiohttp installed')"
$PYTHON_CMD -c "import fitz; print('✓ PyMuPDF installed')"
$PYTHON_CMD -c "import requests; print('✓ requests installed')"

echo ""
echo "================================================"
echo "  Installation Complete!"
echo "================================================"
echo ""
echo "Next steps:"
echo "1. Set GOOGLE_TRANSLATE_API_KEY in your .env file"
echo "2. Run: php artisan queue:work"
echo "3. Upload a PDF to translate"
echo ""
echo "For testing:"
echo "  python test_optimization.py"
echo ""
