<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleTranslateService
{
    private string $apiKey;
    private string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.google_translate.api_key');
        $this->endpoint = 'https://translation.googleapis.com/language/translate/v2';
    }

    /**
     * Traduz um texto usando Google Cloud Translation API
     *
     * @param string $text Texto a ser traduzido
     * @param string $targetLang Idioma de destino (ex: 'pt', 'en', 'es')
     * @param string|null $sourceLang Idioma de origem (null para auto-detectar)
     * @return array
     */
    public function translate(string $text, string $targetLang, ?string $sourceLang = null): array
    {
        try {
            $params = [
                'q' => $text,
                'target' => $targetLang,
                'key' => $this->apiKey,
                'format' => 'text',
            ];

            if ($sourceLang) {
                $params['source'] = $sourceLang;
            }

            $response = Http::asForm()->post($this->endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'translatedText' => $data['data']['translations'][0]['translatedText'] ?? '',
                    'detectedSourceLanguage' => $data['data']['translations'][0]['detectedSourceLanguage'] ?? $sourceLang,
                ];
            }

            Log::error('Google Translate API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao traduzir texto: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Google Translate Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Erro ao conectar com o serviço de tradução: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Traduz múltiplos textos de uma vez
     *
     * @param array $texts Array de textos para traduzir
     * @param string $targetLang Idioma de destino
     * @param string|null $sourceLang Idioma de origem
     * @return array
     */
    public function translateBatch(array $texts, string $targetLang, ?string $sourceLang = null): array
    {
        try {
            $params = [
                'q' => $texts,
                'target' => $targetLang,
                'key' => $this->apiKey,
                'format' => 'text',
            ];

            if ($sourceLang) {
                $params['source'] = $sourceLang;
            }

            $response = Http::asForm()->post($this->endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();
                $translations = [];

                foreach ($data['data']['translations'] as $translation) {
                    $translations[] = [
                        'translatedText' => $translation['translatedText'],
                        'detectedSourceLanguage' => $translation['detectedSourceLanguage'] ?? $sourceLang,
                    ];
                }

                return [
                    'success' => true,
                    'translations' => $translations,
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao traduzir textos: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao conectar com o serviço de tradução: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Detecta o idioma de um texto
     *
     * @param string $text
     * @return array
     */
    public function detectLanguage(string $text): array
    {
        try {
            $endpoint = 'https://translation.googleapis.com/language/translate/v2/detect';

            $response = Http::asForm()->post($endpoint, [
                'q' => $text,
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'language' => $data['data']['detections'][0][0]['language'] ?? 'unknown',
                    'confidence' => $data['data']['detections'][0][0]['confidence'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao detectar idioma',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Translate texts with deduplication and smart batching (optimized for PDFs).
     * Returns array mapping original text => translated text.
     *
     * @param array $texts
     * @param string $targetLang
     * @param string|null $sourceLang
     * @return array
     */
    public function translateTextsOptimized(array $texts, string $targetLang, ?string $sourceLang = null): array
    {
        $startTime = microtime(true);

        // Remove empty texts
        $nonEmptyTexts = array_filter($texts, fn($text) => !empty(trim($text)));

        if (empty($nonEmptyTexts)) {
            return [];
        }

        Log::info('PHP Translation - Starting', [
            'total_texts' => count($texts),
            'non_empty' => count($nonEmptyTexts),
            'target' => $targetLang,
            'source' => $sourceLang ?? 'auto',
        ]);

        // Deduplicate - normalize and map
        $normalizedMap = [];
        foreach ($nonEmptyTexts as $text) {
            $normalized = $this->normalizeText($text);
            if (!isset($normalizedMap[$normalized])) {
                $normalizedMap[$normalized] = [];
            }
            $normalizedMap[$normalized][] = $text;
        }

        $uniqueTexts = array_keys($normalizedMap);

        Log::info('PHP Translation - Deduplication', [
            'original' => count($nonEmptyTexts),
            'unique' => count($uniqueTexts),
            'reduction' => round((1 - count($uniqueTexts) / count($nonEmptyTexts)) * 100, 1) . '%',
        ]);

        // Create smart batches (max 30K chars, 128 texts per batch)
        $batches = $this->createSmartBatches($uniqueTexts);

        Log::info('PHP Translation - Batches created', ['count' => count($batches)]);

        // Translate batches
        $translationCache = [];
        foreach ($batches as $index => $batch) {
            $batchResult = $this->translateBatchInternal($batch, $targetLang, $sourceLang);

            foreach ($batch as $i => $originalText) {
                $translationCache[$originalText] = $batchResult[$i] ?? $originalText;
            }

            Log::debug('PHP Translation - Batch completed', [
                'batch' => ($index + 1) . '/' . count($batches),
                'texts' => count($batch),
            ]);
        }

        // Map back to ALL original texts (including duplicates and empties)
        $results = [];
        foreach ($texts as $originalText) {
            if (empty(trim($originalText))) {
                $results[$originalText] = $originalText;
                continue;
            }

            $normalized = $this->normalizeText($originalText);
            $results[$originalText] = $translationCache[$normalized] ?? $originalText;
        }

        $elapsed = microtime(true) - $startTime;

        Log::info('PHP Translation - Completed', [
            'elapsed_seconds' => round($elapsed, 2),
            'texts_per_second' => round(count($texts) / $elapsed, 2),
        ]);

        return $results;
    }

    /**
     * Internal batch translation that returns array of translated texts.
     */
    private function translateBatchInternal(array $texts, string $targetLang, ?string $sourceLang): array
    {
        try {
            $body = [
                'q' => $texts,
                'target' => $targetLang,
                'format' => 'text',
            ];

            if ($sourceLang) {
                $body['source'] = $sourceLang;
            }

            // API key goes in URL, body as JSON
            $url = $this->endpoint . '?key=' . $this->apiKey;

            $response = Http::timeout(60)
                ->retry(3, 2000)
                ->post($url, $body);

            if ($response->successful()) {
                $data = $response->json();
                $translations = $data['data']['translations'] ?? [];

                return array_map(fn($t) => $t['translatedText'] ?? '', $translations);
            }

            Log::error('PHP Translation - Batch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $texts; // Return originals on error

        } catch (\Exception $e) {
            Log::error('PHP Translation - Exception', [
                'message' => $e->getMessage(),
            ]);

            return $texts;
        }
    }

    /**
     * Normalize text for deduplication.
     */
    private function normalizeText(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $lines = explode("\n", $text);
        $normalizedLines = array_map(function($line) {
            return trim(preg_replace('/\s+/', ' ', $line));
        }, $lines);

        $result = implode("\n", array_filter($normalizedLines));
        return $result ?: trim($text);
    }

    /**
     * Create smart batches (max 30K chars and 128 texts per batch).
     */
    private function createSmartBatches(array $texts): array
    {
        $batches = [];
        $currentBatch = [];
        $currentChars = 0;
        $maxChars = 30000;
        $maxTexts = 128;

        foreach ($texts as $text) {
            $textLength = strlen($text);

            if (!empty($currentBatch) &&
                ($currentChars + $textLength > $maxChars || count($currentBatch) >= $maxTexts)) {
                $batches[] = $currentBatch;
                $currentBatch = [];
                $currentChars = 0;
            }

            $currentBatch[] = $text;
            $currentChars += $textLength;
        }

        if (!empty($currentBatch)) {
            $batches[] = $currentBatch;
        }

        return $batches;
    }

    /**
     * Lista idiomas suportados
     *
     * @param string|null $targetLang Idioma para retornar nomes
     * @return array
     */
    public function getSupportedLanguages(?string $targetLang = 'pt'): array
    {
        try {
            $endpoint = 'https://translation.googleapis.com/language/translate/v2/languages';

            $params = [
                'key' => $this->apiKey,
                'target' => $targetLang,
            ];

            $response = Http::get($endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'languages' => $data['data']['languages'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Erro ao obter idiomas suportados',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
