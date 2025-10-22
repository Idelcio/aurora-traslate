<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Services\GoogleTranslateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookTranslateController extends Controller
{
    protected $translateService;

    public function __construct(GoogleTranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    /**
     * Inicia a tradução de um livro específico
     */
    public function translate(Request $request, $bookId)
    {
        try {
            $book = Book::findOrFail($bookId);

            // Verifica se o usuário é o dono do livro
            if ($book->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Você não tem permissão para traduzir este livro.'
                ], 403);
            }

            // Verifica se o livro já foi traduzido
            if ($book->status === 'translated') {
                return response()->json([
                    'success' => false,
                    'error' => 'Este livro já foi traduzido.'
                ], 400);
            }

            // Atualiza status para processing
            $book->update(['status' => 'processing']);

            // Caminho do PDF original
            $originalPdfPath = storage_path('app/public/' . $book->pdf_path);

            if (!file_exists($originalPdfPath)) {
                $book->update(['status' => 'error']);
                return response()->json([
                    'success' => false,
                    'error' => 'Arquivo PDF original não encontrado.'
                ], 404);
            }

            // Gera nomes únicos para os arquivos de trabalho
            $timestamp = time();
            $extractedJsonPath = storage_path('app/public/pdfs/temp/' . $timestamp . '_extracted.json');
            $translatedJsonPath = storage_path('app/public/pdfs/temp/' . $timestamp . '_translated.json');
            $outputPdfPath = storage_path('app/public/pdfs/translated/' . $timestamp . '_translated.pdf');

            // Passo 1: Extrai texto do PDF
            Log::info('Extraindo texto do PDF: ' . $originalPdfPath);
            $extractResult = $this->extractPdfText($originalPdfPath, $extractedJsonPath);

            if (!$extractResult['success']) {
                $book->update(['status' => 'error']);
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
                $book->target_language,
                $book->source_language === 'auto' ? null : $book->source_language
            );

            if (!$translateResult['success']) {
                $book->update(['status' => 'error']);
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao traduzir texto: ' . $translateResult['error']
                ], 500);
            }

            // Passo 3: Reconstrói o PDF com texto traduzido
            Log::info('Reconstruindo PDF com texto traduzido');
            $rebuildResult = $this->rebuildPdf(
                $originalPdfPath,
                $extractedJsonPath,
                $translatedJsonPath,
                $outputPdfPath
            );

            if (!$rebuildResult['success']) {
                $book->update(['status' => 'error']);
                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao reconstruir PDF: ' . $rebuildResult['error']
                ], 500);
            }

            // Atualiza o registro do livro
            $book->update([
                'status' => 'translated',
                'translated_pdf_path' => 'pdfs/translated/' . basename($outputPdfPath)
            ]);

            // Limpa arquivos temporários
            if (file_exists($extractedJsonPath)) unlink($extractedJsonPath);
            if (file_exists($translatedJsonPath)) unlink($translatedJsonPath);

            return response()->json([
                'success' => true,
                'message' => 'Livro traduzido com sucesso!',
                'book_id' => $book->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar tradução de livro: ' . $e->getMessage());

            if (isset($book)) {
                $book->update(['status' => 'error']);
            }

            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar tradução: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrai texto do PDF usando Node.js
     */
    private function extractPdfText($pdfPath, $outputJsonPath)
    {
        $scriptPath = base_path('scripts/extractPdfText_simple.cjs');

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

                // Traduz usando o GoogleTranslateService
                $result = $this->translateService->translate($text, $targetLang, $sourceLang);

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
                'totalTexts' => $totalTexts
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
}
