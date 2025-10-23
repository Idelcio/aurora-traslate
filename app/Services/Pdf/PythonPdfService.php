<?php

namespace App\Services\Pdf;

use App\Services\GoogleTranslateService;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Unified Python PDF Service
 * Handles extraction, translation, and rebuild using Python scripts only.
 * Provides better performance than Node.js+Python hybrid approach.
 */
class PythonPdfService
{
    private string $pythonBinary;
    private string $scriptsPath;
    private GoogleTranslateService $translateService;

    public function __construct(GoogleTranslateService $translateService)
    {
        $this->pythonBinary = config('services.python_translate.binary', 'python');
        $this->scriptsPath = base_path('scripts/python');
        $this->translateService = $translateService;
    }

    /**
     * Complete PDF translation pipeline using Python only.
     *
     * @param string $originalPdfPath Path to original PDF
     * @param string $outputPdfPath Path to save translated PDF
     * @param string $targetLanguage Target language code (e.g., 'pt', 'en')
     * @param string|null $sourceLanguage Source language code or null for auto-detect
     * @param callable|null $progressCallback Optional callback for progress updates
     * @param int|null $maxPages Maximum number of pages to translate (null = all pages)
     * @return array{success: bool, stats: array}
     */
    public function translatePdf(
        string $originalPdfPath,
        string $outputPdfPath,
        string $targetLanguage,
        ?string $sourceLanguage = null,
        ?callable $progressCallback = null,
        ?int $maxPages = null
    ): array {
        $timestamp = time();
        $tempDir = storage_path('app/public/pdfs/temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $extractedJsonPath = "{$tempDir}/{$timestamp}_extracted.json";
        $translatedJsonPath = "{$tempDir}/{$timestamp}_translated.json";

        try {
            // Step 1: Extract PDF text
            Log::info('Step 1/3: Extracting PDF text', [
                'pdf' => $originalPdfPath,
                'max_pages' => $maxPages ?? 'all',
            ]);
            $this->extractPdfText($originalPdfPath, $extractedJsonPath, $maxPages);

            // Step 2: Translate texts
            Log::info('Step 2/3: Translating text');
            $stats = $this->translateTexts(
                $extractedJsonPath,
                $translatedJsonPath,
                $targetLanguage,
                $sourceLanguage,
                $progressCallback
            );

            // Step 3: Rebuild PDF
            Log::info('Step 3/3: Rebuilding PDF with translations');
            $this->rebuildPdf(
                $originalPdfPath,
                $extractedJsonPath,
                $translatedJsonPath,
                $outputPdfPath
            );

            // Cleanup temp files
            $this->cleanup([$extractedJsonPath, $translatedJsonPath]);

            return [
                'success' => true,
                'stats' => $stats,
            ];

        } catch (\Throwable $e) {
            // Cleanup on error
            $this->cleanup([$extractedJsonPath, $translatedJsonPath]);
            throw $e;
        }
    }

    /**
     * Extract text and metadata from PDF using Python/PyMuPDF with OCR.
     */
    private function extractPdfText(string $pdfPath, string $outputJsonPath, ?int $maxPages = null): void
    {
        // Use OCR-always script for best results with scanned/image PDFs
        $scriptPath = "{$this->scriptsPath}/extract_pdf_with_ocr_always.py";

        // Fallback to hybrid OCR
        if (!file_exists($scriptPath)) {
            $scriptPath = "{$this->scriptsPath}/extract_pdf_text_with_ocr.py";
        }

        // Final fallback to regular extraction
        if (!file_exists($scriptPath)) {
            $scriptPath = "{$this->scriptsPath}/extract_pdf_text.py";
        }

        if (!file_exists($scriptPath)) {
            throw new RuntimeException("Python extraction script not found: {$scriptPath}");
        }

        // Normalize paths for Windows
        $scriptPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $scriptPath);
        $pdfPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $pdfPath);
        $outputJsonPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $outputJsonPath);

        $command = [
            $this->pythonBinary,
            $scriptPathNorm,
            $pdfPathNorm,
            $outputJsonPathNorm,
        ];

        // Add OCR parameters for the always-OCR script
        if (str_contains($scriptPath, 'ocr_always')) {
            $command[] = '2.0';  // zoom
            $command[] = '4';    // max workers for parallel OCR

            // Add max_pages if specified
            if ($maxPages !== null) {
                $command[] = (string) $maxPages;
            }
        }

        $process = new Process($command, base_path());

        $process->setTimeout(1800); // 30 minutes for large PDFs with OCR
        $process->run();

        // Log the command and output for debugging
        Log::info('PDF extraction command', [
            'command' => $process->getCommandLine(),
            'exit_code' => $process->getExitCode(),
            'stdout' => $process->getOutput(),
            'stderr' => $process->getErrorOutput(),
        ]);

        if (!$process->isSuccessful()) {
            Log::error('PDF extraction failed', [
                'command' => $process->getCommandLine(),
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
            ]);
            throw new ProcessFailedException($process);
        }

        if (!file_exists($outputJsonPath)) {
            throw new RuntimeException('Extracted JSON file was not created at: ' . $outputJsonPath);
        }
    }

    /**
     * Translate texts using PHP (bypasses Python network issues).
     */
    private function translateTexts(
        string $extractedJsonPath,
        string $translatedJsonPath,
        string $targetLanguage,
        ?string $sourceLanguage,
        ?callable $progressCallback
    ): array {
        $startTime = microtime(true);

        // Load extracted JSON
        $extracted = json_decode(file_get_contents($extractedJsonPath), true);

        if (!$extracted || !isset($extracted['pages'])) {
            throw new RuntimeException('Invalid extracted JSON structure');
        }

        // Collect all texts from all pages
        $allTexts = [];
        foreach ($extracted['pages'] as $page) {
            foreach ($page['textItems'] ?? [] as $item) {
                $allTexts[] = $item['text'] ?? '';
            }
        }

        Log::info('Translating texts via PHP', [
            'total_texts' => count($allTexts),
            'target' => $targetLanguage,
            'source' => $sourceLanguage ?? 'auto',
        ]);

        // Translate using PHP service (bypasses Python DNS issues)
        $translations = $this->translateService->translateTextsOptimized(
            $allTexts,
            $targetLanguage,
            $sourceLanguage
        );

        // Build output structure
        $output = [
            'numPages' => $extracted['numPages'] ?? count($extracted['pages']),
            'pages' => [],
        ];

        $textIndex = 0;
        foreach ($extracted['pages'] as $page) {
            $outputPage = [
                'pageNumber' => $page['pageNumber'] ?? 1,
                'textItems' => [],
            ];

            foreach ($page['textItems'] ?? [] as $item) {
                $originalText = $item['text'] ?? '';
                $translatedText = $translations[$originalText] ?? $originalText;

                $outputPage['textItems'][] = [
                    'originalText' => $originalText,
                    'translatedText' => $translatedText,
                ];

                $textIndex++;
            }

            $output['pages'][] = $outputPage;
        }

        // Save translated JSON
        file_put_contents($translatedJsonPath, json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $elapsed = microtime(true) - $startTime;

        $stats = [
            'totalPages' => $output['numPages'],
            'totalTexts' => count($allTexts),
            'totalChars' => array_sum(array_map('strlen', $allTexts)),
            'uniqueTexts' => count(array_unique($allTexts)),
            'elapsedSeconds' => round($elapsed, 2),
            'textsPerSecond' => round(count($allTexts) / $elapsed, 2),
        ];

        Log::info('Translation completed (PHP)', $stats);

        return $stats;
    }

    /**
     * OLD METHOD - Translate texts using Python script (has DNS issues).
     */
    private function translateTextsPython(
        string $extractedJsonPath,
        string $translatedJsonPath,
        string $targetLanguage,
        ?string $sourceLanguage,
        ?callable $progressCallback
    ): array {
        $useOptimized = config('services.python_translate.use_optimized', true);
        // Use threading version instead of asyncio for Windows compatibility
        $scriptName = $useOptimized ? 'translate_and_format_threading.py' : 'translate_and_format.py';
        $scriptPath = "{$this->scriptsPath}/{$scriptName}";

        if (!file_exists($scriptPath)) {
            throw new RuntimeException("Python translation script not found: {$scriptPath}");
        }

        // Normalize all paths for Windows
        $scriptPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $scriptPath);
        $extractedJsonPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $extractedJsonPath);
        $translatedJsonPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $translatedJsonPath);

        $command = [
            $this->pythonBinary,
            $scriptPathNorm,
            '--input-json', $extractedJsonPathNorm,
            '--output-json', $translatedJsonPathNorm,
            '--target-language', $targetLanguage,
        ];

        // ALWAYS include source-language, even if null/auto (Google API needs it for proper detection)
        if (!empty($sourceLanguage)) {
            $command[] = '--source-language';
            $command[] = $sourceLanguage;
        }

        $apiKey = config('services.google_translate.api_key');
        if (!empty($apiKey)) {
            $command[] = '--api-key';
            $command[] = $apiKey;
        }

        if ($useOptimized) {
            $maxConcurrent = config('services.python_translate.max_concurrent', 5);
            $command[] = '--max-concurrent';
            $command[] = (string) $maxConcurrent;

            // TEMPORARILY DISABLE PROGRESS - causes buffering issues
            // if ($progressCallback !== null) {
            //     $command[] = '--progress';
            // }
        }

        // Log the full command for debugging
        Log::info('Starting translation', [
            'command' => implode(' ', array_map(function($arg) {
                return strpos($arg, ' ') !== false ? '"' . $arg . '"' : $arg;
            }, $command)),
            'input_exists' => file_exists($extractedJsonPath),
            'input_size' => file_exists($extractedJsonPath) ? filesize($extractedJsonPath) : 0,
        ]);

        $process = new Process($command, base_path(), $_ENV); // Set working directory and pass environment
        $process->setTimeout(null);

        // SIMPLIFIED: Just run without streaming for now
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Translation failed', [
                'command' => $process->getCommandLine(),
                'exit_code' => $process->getExitCode(),
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
            ]);

            $output = $process->getOutput();
            $stderr = $process->getErrorOutput();
            $errorMsg = 'Translation script failed';

            if (!empty($stderr)) {
                $errorMsg .= ": " . $stderr;
            } elseif (strpos($output, 'ERROR:') !== false) {
                $errorMsg .= ": " . $output;
            }

            throw new RuntimeException($errorMsg);
        }

        // VERIFY TRANSLATION ACTUALLY HAPPENED
        if (file_exists($translatedJsonPath)) {
            $translatedContent = json_decode(file_get_contents($translatedJsonPath), true);
            $sampleTexts = [];

            if (isset($translatedContent['pages'][0]['textItems'])) {
                $items = array_slice($translatedContent['pages'][0]['textItems'], 0, 3);
                foreach ($items as $item) {
                    $sampleTexts[] = [
                        'original' => $item['originalText'] ?? '',
                        'translated' => $item['translatedText'] ?? '',
                    ];
                }
            }

            Log::info('Translation completed - Sample check', [
                'output_file_exists' => true,
                'output_file_size' => filesize($translatedJsonPath),
                'sample_translations' => $sampleTexts,
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(), // Include stderr for debug logs
            ]);
        } else {
            Log::error('Translation JSON not created', [
                'expected_path' => $translatedJsonPath,
            ]);
        }

        return $this->extractStats($process->getOutput());
    }

    /**
     * Rebuild PDF with translated text using Python/PyMuPDF or fpdf2.
     */
    private function rebuildPdf(
        string $originalPdfPath,
        string $extractedJsonPath,
        string $translatedJsonPath,
        string $outputPdfPath
    ): void {
        // Use SIMPLE PDF creation (just translated text, no fancy layout)
        $scriptPath = "{$this->scriptsPath}/create_simple_translated_pdf.py";

        if (!file_exists($scriptPath)) {
            throw new RuntimeException("Python rebuild script not found: {$scriptPath}");
        }

        // Normalize paths for Windows
        $scriptPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $scriptPath);
        $translatedJsonPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $translatedJsonPath);
        $outputPdfPathNorm = str_replace('/', DIRECTORY_SEPARATOR, $outputPdfPath);

        // Simple version only needs translated JSON and output path
        $process = new Process([
            $this->pythonBinary,
            $scriptPathNorm,
            $translatedJsonPathNorm,
            $outputPdfPathNorm,
        ], base_path()); // Set working directory

        $process->setTimeout(600); // 10 minutes

        Log::info('Starting PDF rebuild', [
            'script' => $scriptPathNorm,
            'translated_json' => $translatedJsonPathNorm,
            'output_pdf' => $outputPdfPathNorm,
            'translated_json_exists' => file_exists($translatedJsonPath),
            'translated_json_size' => file_exists($translatedJsonPath) ? filesize($translatedJsonPath) : 0,
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('PDF rebuild failed', [
                'command' => $process->getCommandLine(),
                'exit_code' => $process->getExitCode(),
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
            ]);
            throw new ProcessFailedException($process);
        }

        if (!file_exists($outputPdfPath)) {
            throw new RuntimeException('Output PDF file was not created');
        }

        Log::info('PDF rebuild completed', [
            'output_pdf' => $outputPdfPath,
            'output_size' => filesize($outputPdfPath),
            'stdout' => $process->getOutput(),
            'stderr' => $process->getErrorOutput(),
        ]);
    }

    /**
     * Handle progress output from Python script.
     */
    private function handleProgressOutput(string $output, callable $callback): void
    {
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $decoded = json_decode($line, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $type = $decoded['type'] ?? null;

                if ($type === 'progress' && isset($decoded['data'])) {
                    $callback($decoded['data']);
                }
            }
        }
    }

    /**
     * Extract statistics from script output.
     */
    private function extractStats(string $stdout): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $stdout)));

        foreach (array_reverse($lines) as $line) {
            $decoded = json_decode($line, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                if (isset($decoded['type']) && $decoded['type'] === 'result' && isset($decoded['stats'])) {
                    return $decoded['stats'];
                }

                if (isset($decoded['stats'])) {
                    return $decoded['stats'];
                }
            }
        }

        return [];
    }

    /**
     * Cleanup temporary files.
     */
    private function cleanup(array $paths): void
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
