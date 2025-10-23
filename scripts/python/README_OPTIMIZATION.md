# Python Translation Optimization

## Setup

### 1. Install Python Dependencies

```bash
pip install -r scripts/python/requirements.txt
```

### 2. Configure Environment

Add to your `.env`:

```
PYTHON_TRANSLATE_USE_OPTIMIZED=true
PYTHON_TRANSLATE_MAX_CONCURRENT=5
```

## Performance Improvements

### Original Version
- Sequential batch processing
- Synchronous HTTP requests
- No deduplication before translation
- Fixed batch size (64 texts)

### Optimized Version
- **Async/await with aiohttp** - Concurrent requests (5 by default)
- **Smart batching** - Dynamic batches based on character count (30K chars)
- **Pre-normalization** - Reduces duplicate texts before translation
- **Connection pooling** - Reuses HTTP connections
- **Real-time progress** - JSON Lines output for tracking
- **Intelligent retry** - Exponential backoff per batch

## Expected Performance Gains

- **2-5x faster** for documents with many duplicate texts
- **3-10x faster** for large documents (100+ pages) due to concurrent requests
- **Reduced API costs** through deduplication

## Testing

### Quick Test
```bash
php test_translate.php
```

### Manual Comparison
```bash
# Original
time python scripts/python/translate_and_format.py   --input-json /path/to/extracted.json   --output-json /path/to/translated.json   --target-language pt

# Optimized
time python scripts/python/translate_and_format_optimized.py   --input-json /path/to/extracted.json   --output-json /path/to/translated_opt.json   --target-language pt   --max-concurrent 5   --progress
```

## Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| PYTHON_TRANSLATE_USE_OPTIMIZED | true | Use optimized async version |
| PYTHON_TRANSLATE_MAX_CONCURRENT | 5 | Max concurrent API requests |

## Troubleshooting

### aiohttp not found
```bash
pip install aiohttp
```

### Slower performance
- Increase MAX_CONCURRENT (try 10)
- Check network latency
- Verify API key quota

