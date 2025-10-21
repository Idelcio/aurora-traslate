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
