<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\Pdf\PythonPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;
    public $tries = 1;

    public function __construct(
        protected Book $book
    ) {
    }

    public function handle(PythonPdfService $pythonPdfService): void
    {
        try {
            Log::info('Iniciando traducao do livro (Python pipeline)', [
                'book_id' => $this->book->id,
                'title' => $this->book->title,
            ]);

            $originalPdfPath = storage_path('app/public/' . $this->book->pdf_path);

            if (!file_exists($originalPdfPath)) {
                throw new \RuntimeException('Arquivo PDF original nao encontrado');
            }

            $timestamp = time();
            $safeBaseName = Str::slug($this->book->title, '-') ?: 'livro';
            $outputPdfFilename = "{$timestamp}_{$safeBaseName}_traduzido.pdf";
            $outputPdfPath = storage_path('app/public/pdfs/translated/' . $outputPdfFilename);

            $this->ensureDirectory(dirname($outputPdfPath));

            // Use unified Python service for entire pipeline
            // Always pass source language - use 'pt-BR' as default if 'auto'
            $sourceLanguage = $this->book->source_language === 'auto' ? 'pt-BR' : $this->book->source_language;

            $result = $pythonPdfService->translatePdf(
                $originalPdfPath,
                $outputPdfPath,
                $this->book->target_language,
                $sourceLanguage,
                function ($progress) {
                    // Log progress updates
                    Log::info('Translation progress', [
                        'book_id' => $this->book->id,
                        'progress' => $progress['progress'] ?? 0,
                        'completed' => $progress['completedBatches'] ?? 0,
                        'total' => $progress['totalBatches'] ?? 0,
                    ]);
                }
            );

            $this->book->update([
                'status' => 'translated',
                'translated_pdf_path' => 'pdfs/translated/' . $outputPdfFilename,
            ]);

            Log::info('Traducao concluida com sucesso', [
                'book_id' => $this->book->id,
                'stats' => $result['stats'] ?? [],
            ]);

        } catch (\Throwable $throwable) {
            Log::error('Erro ao traduzir livro', [
                'book_id' => $this->book->id,
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);

            $this->book->update(['status' => 'error']);
            throw $throwable;
        }
    }

    private function ensureDirectory(string $directory): void
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
