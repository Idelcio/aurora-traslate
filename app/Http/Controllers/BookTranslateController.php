<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\Pdf\PythonPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookTranslateController extends Controller
{
    public function __construct(
        protected PythonPdfService $pythonPdfService
    ) {
    }

    /**
     * Starts the translation for a specific book.
     */
    public function translate(Request $request, int $bookId): JsonResponse
    {
        try {
            $book = Book::findOrFail($bookId);

            if ($book->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Voce nao tem permissao para traduzir este livro.',
                ], 403);
            }

            if ($book->status === 'translated') {
                return response()->json([
                    'success' => false,
                    'error' => 'Este livro ja foi traduzido.',
                ], 400);
            }

            $book->update(['status' => 'processing']);

            $originalPdfPath = storage_path('app/public/' . $book->pdf_path);

            if (!file_exists($originalPdfPath)) {
                $book->update(['status' => 'error']);

                return response()->json([
                    'success' => false,
                    'error' => 'Arquivo PDF original nao encontrado.',
                ], 404);
            }

            $timestamp = time();
            $safeBaseName = Str::slug($book->title, '-') ?: 'livro';
            $outputPdfFilename = "{$timestamp}_{$safeBaseName}_traduzido.pdf";
            $outputPdfPath = storage_path('app/public/pdfs/translated/' . $outputPdfFilename);

            $this->ensureDirectory(dirname($outputPdfPath));

            // Use unified Python PDF service for entire pipeline
            Log::info('Iniciando traducao completa via Python', ['book_id' => $book->id]);

            // Always pass source language - use 'pt-BR' as default if 'auto'
            // Google Translate API needs source language for accurate translations
            $sourceLanguage = $book->source_language === 'auto' ? 'pt-BR' : $book->source_language;

            // Get max_pages from request (optional page limit)
            $maxPages = request()->input('max_pages') ? (int) request()->input('max_pages') : null;

            $result = $this->pythonPdfService->translatePdf(
                $originalPdfPath,
                $outputPdfPath,
                $book->target_language,
                $sourceLanguage,
                null, // progressCallback
                $maxPages
            );

            $book->update([
                'status' => 'translated',
                'translated_pdf_path' => 'pdfs/translated/' . $outputPdfFilename,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Livro traduzido com sucesso!',
                'book_id' => $book->id,
                'stats' => $result['stats'] ?? [],
            ]);
        } catch (\Throwable $throwable) {
            Log::error('Erro ao processar traducao de livro', [
                'book_id' => $bookId ?? null,
                'message' => $throwable->getMessage(),
            ]);

            if (isset($book) && $book->status !== 'error') {
                $book->update(['status' => 'error']);
            }

            return response()->json([
                'success' => false,
                'error' => 'Erro ao processar traducao: ' . $throwable->getMessage(),
            ], 500);
        }
    }

    private function ensureDirectory(string $directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
