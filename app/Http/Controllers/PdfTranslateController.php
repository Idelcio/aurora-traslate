<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleTranslateService;
use Illuminate\Support\Facades\Log;

class PdfTranslateController extends Controller
{
    protected $translateService;

    public function __construct(GoogleTranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    /**
     * Processa a tradução de um PDF
     */
    public function translate(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:51200', // max 50MB
            'target_language' => 'required|string|max:10',
            'source_language' => 'nullable|string|max:10',
        ]);

        try {
            $pdf = $request->file('pdf');
            $targetLang = $request->target_language;
            $sourceLang = $request->source_language ?? 'auto';

            // Gera nomes únicos para os arquivos
            $timestamp = time();
            $originalFilename = $timestamp . '_original.pdf';
            $extractedJsonFilename = $timestamp . '_extracted.json';
            $translatedJsonFilename = $timestamp . '_translated.json';
            $outputPdfFilename = $timestamp . '_translated.pdf';

            // Salva o PDF original
            $pdfPath = $pdf->storeAs('pdfs/temp', $originalFilename, 'public');
            $fullPdfPath = storage_path('app/public/' . $pdfPath);

            // Caminho para os arquivos de trabalho
            $extractedJsonPath = storage_path('app/public/pdfs/temp/' . $extractedJsonFilename);
            $translatedJsonPath = storage_path('app/public/pdfs/temp/' . $translatedJsonFilename);
            $outputPdfPath = storage_path('app/public/pdfs/translated/' . $outputPdfFilename);

            // Cria diretório de saída se não existir
            if (!file_exists(dirname($outputPdfPath))) {
                mkdir(dirname($outputPdfPath), 0755, true);
            }

            // Passo 1: Extrai texto do PDF
            Log::info('Extraindo texto do PDF: ' . $fullPdfPath);
            $extractResult = $this->extractPdfText($fullPdfPath, $extractedJsonPath);

            if (!$extractResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao extrair texto do PDF: ' . $extractResult['error']
                ], 500);
            }

            // Passo 2: Traduz o texto extraído
            Log::info('Traduzindo texto extraído');
            $translateResult = $this->translateExtractedText(
                $extractedJsonPath,
                $translatedJsonPath,
                $targetLang,
                $sourceLang
            );

            if (!$translateResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao traduzir texto: ' . $translateResult['error']
                ], 500);
            }

            // Passo 3: Reconstrói o PDF com texto traduzido
            Log::info('Reconstruindo PDF com texto traduzido');
            $rebuildResult = $this->rebuildPdf(
                $fullPdfPath,
                $extractedJsonPath,
                $translatedJsonPath,
                $outputPdfPath
            );

            if (!$rebuildResult['success']) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao reconstruir PDF: ' . $rebuildResult['error']
                ], 500);
            }

            // Limpa arquivos temporários
            Storage::disk('public')->delete($pdfPath);
            unlink($extractedJsonPath);
            unlink($translatedJsonPath);

            // Retorna o PDF traduzido
            return response()->json([
                'success' => true,
                'filename' => $outputPdfFilename,
                'download_url' => route('pdf.translate.download', ['filename' => $outputPdfFilename]),
                'stats' => $translateResult['stats']
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar tradução de PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrai texto do PDF usando Node.js
     */
    private function extractPdfText($pdfPath, $outputJsonPath)
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
                'error' => implode("\n", $output)
            ];
        }

        return ['success' => true];
    }

    /**
     * Traduz o texto extraído
     */
    private function translateExtractedText($extractedJsonPath, $outputJsonPath, $targetLang, $sourceLang)
    {
        $extractedData = json_decode(file_get_contents($extractedJsonPath), true);

        $translatedData = [
            'numPages' => $extractedData['numPages'],
            'pages' => []
        ];

        $totalTexts = 0;
        $totalChars = 0;

        // Processa cada página
        foreach ($extractedData['pages'] as $page) {
            $translatedPage = [
                'pageNumber' => $page['pageNumber'],
                'textItems' => []
            ];

            // Traduz cada item de texto
            foreach ($page['textItems'] as $item) {
                $text = $item['text'];
                $totalTexts++;
                $totalChars += strlen($text);

                // Traduz usando o GoogleTranslateService
                $result = $this->translateService->translate($text, $targetLang, $sourceLang === 'auto' ? null : $sourceLang);

                if ($result['success']) {
                    $translatedPage['textItems'][] = [
                        'originalText' => $text,
                        'translatedText' => $result['translatedText']
                    ];
                } else {
                    // Se falhar, mantém o texto original
                    Log::warning('Falha ao traduzir texto: ' . $text);
                    $translatedPage['textItems'][] = [
                        'originalText' => $text,
                        'translatedText' => $text
                    ];
                }

                // Pequeno delay para evitar rate limiting
                usleep(100000); // 100ms
            }

            $translatedData['pages'][] = $translatedPage;
        }

        file_put_contents($outputJsonPath, json_encode($translatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return [
            'success' => true,
            'stats' => [
                'totalPages' => $extractedData['numPages'],
                'totalTexts' => $totalTexts,
                'totalChars' => $totalChars
            ]
        ];
    }

    /**
     * Reconstrói o PDF com texto traduzido
     */
    private function rebuildPdf($originalPdfPath, $extractedJsonPath, $translatedJsonPath, $outputPdfPath)
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
                'error' => implode("\n", $output)
            ];
        }

        return ['success' => true];
    }

    /**
     * Download do PDF traduzido
     */
    public function download($filename)
    {
        $path = storage_path('app/public/pdfs/translated/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado');
        }

        return response()->download($path);
    }
}
