<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Book;
use App\Services\GoogleTranslateService;
use Illuminate\Support\Facades\Log;

class TranslatePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $book;
    public $timeout = 1800; // 30 minutos
    public $tries = 1; // Não retentar automaticamente

    /**
     * Create a new job instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleTranslateService $translateService): void
    {
        try {
            Log::info("Iniciando tradução do livro ID: {$this->book->id}");

            // Caminho do PDF original
            $originalPdfPath = storage_path('app/public/' . $this->book->pdf_path);

            if (!file_exists($originalPdfPath)) {
                throw new \Exception('Arquivo PDF original não encontrado');
            }

            // Gera nomes únicos para arquivos de trabalho
            $timestamp = time();
            $extractedJsonPath = storage_path('app/public/pdfs/temp/' . $timestamp . '_extracted.json');
            $translatedJsonPath = storage_path('app/public/pdfs/temp/' . $timestamp . '_translated.json');
            $outputPdfPath = storage_path('app/public/pdfs/translated/' . $timestamp . '_translated.pdf');

            // Passo 1: Extrai texto
            Log::info("Extraindo texto do PDF");
            $this->extractPdfText($originalPdfPath, $extractedJsonPath);

            // Passo 2: Traduz
            Log::info("Traduzindo texto");
            $this->translateExtractedText(
                $extractedJsonPath,
                $translatedJsonPath,
                $this->book->target_language,
                $this->book->source_language === 'auto' ? null : $this->book->source_language,
                $translateService
            );

            // Passo 3: Reconstrói PDF
            Log::info("Reconstruindo PDF");
            $this->rebuildPdf($originalPdfPath, $extractedJsonPath, $translatedJsonPath, $outputPdfPath);

            // Atualiza o registro
            $this->book->update([
                'status' => 'translated',
                'translated_pdf_path' => 'pdfs/translated/' . basename($outputPdfPath)
            ]);

            // Limpa temporários
            if (file_exists($extractedJsonPath)) unlink($extractedJsonPath);
            if (file_exists($translatedJsonPath)) unlink($translatedJsonPath);

            Log::info("Tradução concluída com sucesso para o livro ID: {$this->book->id}");

        } catch (\Exception $e) {
            Log::error("Erro ao traduzir livro ID {$this->book->id}: " . $e->getMessage());
            $this->book->update(['status' => 'error']);
            throw $e;
        }
    }

    private function extractPdfText($pdfPath, $outputJsonPath)
    {
        $scriptPath = base_path('scripts/extractPdfText_simple.cjs');
        $command = sprintf('node "%s" "%s" "%s" 2>&1', $scriptPath, $pdfPath, $outputJsonPath);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Erro ao extrair texto: ' . implode("\n", $output));
        }
    }

    private function translateExtractedText($extractedJsonPath, $outputJsonPath, $targetLang, $sourceLang, $translateService)
    {
        $extractedData = json_decode(file_get_contents($extractedJsonPath), true);

        $translatedData = [
            'numPages' => $extractedData['numPages'],
            'pages' => []
        ];

        foreach ($extractedData['pages'] as $page) {
            $translatedPage = [
                'pageNumber' => $page['pageNumber'],
                'textItems' => []
            ];

            foreach ($page['textItems'] as $item) {
                $result = $translateService->translate($item['text'], $targetLang, $sourceLang);

                $translatedPage['textItems'][] = [
                    'originalText' => $item['text'],
                    'translatedText' => $result['success'] ? $result['translatedText'] : $item['text']
                ];

                usleep(100000); // 100ms delay
            }

            $translatedData['pages'][] = $translatedPage;
        }

        file_put_contents($outputJsonPath, json_encode($translatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function rebuildPdf($originalPdfPath, $extractedJsonPath, $translatedJsonPath, $outputPdfPath)
    {
        // Lê os dados extraídos para pegar o número de páginas
        $extractedData = json_decode(file_get_contents($extractedJsonPath), true);
        $numPages = $extractedData['numPages'] ?? 1;

        $scriptPath = base_path('scripts/rebuildSimplePdf.cjs');
        $command = sprintf('node "%s" "%s" "%s" %d 2>&1', $scriptPath, $translatedJsonPath, $outputPdfPath, $numPages);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Erro ao reconstruir PDF: ' . implode("\n", $output));
        }
    }
}
