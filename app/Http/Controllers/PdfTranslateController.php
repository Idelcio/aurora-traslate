<?php

namespace App\Http\Controllers;

use App\Services\Pdf\PythonTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PdfTranslateController extends Controller
{
    public function __construct(
        protected PythonTranslator $pythonTranslator
    ) {
    }

    /**
     * Handles the PDF translation workflow.
     */
    public function translate(Request $request)
    {
        $allowedSourceLanguages = config('translation.source_languages', []);
        $allowedTargetLanguages = config('translation.target_languages', []);

        $request->validate([
            'pdf' => 'required|mimes:pdf|max:51200', // 50MB
            'target_language' => [
                'required',
                'string',
                'max:10',
                Rule::in($allowedTargetLanguages),
            ],
            'source_language' => [
                'nullable',
                'string',
                'max:10',
                Rule::in($allowedSourceLanguages),
            ],
        ]);

        try {
            $pdf = $request->file('pdf');
            $targetLang = $request->target_language;
            $defaultSource = config('translation.defaults.source_language', 'auto');
            $sourceLang = $request->source_language ?? $defaultSource;

            $originalBaseName = pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME);
            $safeBaseName = Str::slug($originalBaseName, '-') ?: 'documento';

            $timestamp = time();
            $originalFilename = "{$timestamp}_original.pdf";
            $extractedJsonFilename = "{$timestamp}_extracted.json";
            $translatedJsonFilename = "{$timestamp}_translated.json";
            $outputPdfFilename = "{$timestamp}_{$safeBaseName}_traduzido.pdf";
            $downloadName = "{$originalBaseName} (traduzido).pdf";

            $pdfPath = $pdf->storeAs('pdfs/temp', $originalFilename, 'public');
            $fullPdfPath = storage_path('app/public/' . $pdfPath);

            $extractedJsonPath = storage_path('app/public/pdfs/temp/' . $extractedJsonFilename);
            $translatedJsonPath = storage_path('app/public/pdfs/temp/' . $translatedJsonFilename);
            $outputPdfPath = storage_path('app/public/pdfs/translated/' . $outputPdfFilename);

            $this->ensureDirectory(dirname($extractedJsonPath));
            $this->ensureDirectory(dirname($outputPdfPath));

            Log::info('Extraindo texto do PDF', ['path' => $fullPdfPath]);
            $extractResult = $this->extractPdfText($fullPdfPath, $extractedJsonPath);

            if (!$extractResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao extrair texto do PDF: ' . $extractResult['error'],
                ], 500);
            }

            Log::info('Traduzindo conteudo com Python');
            $translateResult = $this->translateExtractedText(
                $extractedJsonPath,
                $translatedJsonPath,
                $targetLang,
                $sourceLang
            );

            if (!$translateResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao traduzir texto: ' . ($translateResult['error'] ?? 'processo desconhecido'),
                ], 500);
            }

            Log::info('Reconstruindo PDF traduzido');
            $rebuildResult = $this->rebuildPdf(
                $fullPdfPath,
                $extractedJsonPath,
                $translatedJsonPath,
                $outputPdfPath
            );

            if (!$rebuildResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao reconstruir PDF: ' . $rebuildResult['error'],
                ], 500);
            }

            Storage::disk('public')->delete($pdfPath);
            $this->deleteIfExists($extractedJsonPath);
            $this->deleteIfExists($translatedJsonPath);

            return response()->json([
                'success' => true,
                'filename' => $outputPdfFilename,
                'download_url' => route('pdf.translate.download', ['filename' => $outputPdfFilename]),
                'stats' => $translateResult['stats'] ?? [],
                'download_filename' => $downloadName,
            ]);
        } catch (\Throwable $throwable) {
            Log::error('Erro ao processar traducao de PDF', [
                'message' => $throwable->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar PDF: ' . $throwable->getMessage(),
            ], 500);
        }
    }

    private function extractPdfText(string $pdfPath, string $outputJsonPath): array
    {
        $scriptPath = base_path('scripts/extractPdfText.cjs');

        $command = sprintf(
            'node "%s" "%s" "%s" 2>&1',
            $scriptPath,
            $pdfPath,
            $outputJsonPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                'success' => false,
                'error' => implode("\n", $output),
            ];
        }

        return ['success' => true];
    }

    private function translateExtractedText(
        string $extractedJsonPath,
        string $outputJsonPath,
        string $targetLang,
        string $sourceLang
    ): array {
        try {
            return $this->pythonTranslator->translate(
                $extractedJsonPath,
                $outputJsonPath,
                $targetLang,
                $sourceLang === 'auto' ? null : $sourceLang
            );
        } catch (\Throwable $throwable) {
            Log::error('Falha ao executar script Python de traducao', [
                'message' => $throwable->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $throwable->getMessage(),
            ];
        }
    }

    private function rebuildPdf(string $originalPdfPath, string $extractedJsonPath, string $translatedJsonPath, string $outputPdfPath): array
    {
        $scriptPath = base_path('scripts/rebuildPdfWithTranslation.cjs');

        $command = sprintf(
            'node "%s" "%s" "%s" "%s" "%s" 2>&1',
            $scriptPath,
            $originalPdfPath,
            $extractedJsonPath,
            $translatedJsonPath,
            $outputPdfPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            return [
                'success' => false,
                'error' => implode("\n", $output),
            ];
        }

        return ['success' => true];
    }

    public function download(string $filename)
    {
        $path = storage_path('app/public/pdfs/translated/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Arquivo nao encontrado');
        }

        $downloadName = $this->resolveDownloadName($filename);

        return response()->download($path, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function resolveDownloadName(string $filename): string
    {
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $parts = explode('_', $basename, 2);
        $remainder = $parts[1] ?? $parts[0];

        $remainder = preg_replace('/_?traduzido$/i', '', $remainder);
        $readable = Str::of($remainder)
            ->replace('-', ' ')
            ->trim()
            ->title();

        if ($readable->isEmpty()) {
            return 'Documento Traduzido.pdf';
        }

        return "{$readable} (traduzido).pdf";
    }

    private function ensureDirectory(string $directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    private function deleteIfExists(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
