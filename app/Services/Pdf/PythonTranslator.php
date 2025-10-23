<?php

namespace App\Services\Pdf;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PythonTranslator
{
    /**
     * Executa o script Python de traducao em lote.
     *
     * @param callable|null $progressCallback Callback para progresso: function(array $progress): void
     * @return array{success: bool, stats?: array<string, mixed>}
     */
    public function translate(
        string $inputJsonPath,
        string $outputJsonPath,
        string $targetLanguage,
        ?string $sourceLanguage = null,
        ?callable $progressCallback = null
    ): array {
        // Try optimized version first, fallback to original
        $optimizedScript = base_path('scripts/python/translate_and_format_optimized.py');
        $originalScript = base_path('scripts/python/translate_and_format.py');

        $useOptimized = file_exists($optimizedScript) &&
                       config('services.python_translate.use_optimized', true);

        $scriptPath = $useOptimized ? $optimizedScript : $originalScript;

        if (!file_exists($scriptPath)) {
            throw new RuntimeException("Script Python nao encontrado em {$scriptPath}");
        }

        $pythonBinary = config('services.python_translate.binary')
            ?? env('PYTHON_TRANSLATE_BINARY', 'python');

        $command = [
            $pythonBinary,
            $scriptPath,
            '--input-json',
            $inputJsonPath,
            '--output-json',
            $outputJsonPath,
            '--target-language',
            $targetLanguage,
        ];

        if (!empty($sourceLanguage) && $sourceLanguage !== 'auto') {
            $command[] = '--source-language';
            $command[] = $sourceLanguage;
        }

        $apiKey = config('services.google_translate.api_key');
        if (!empty($apiKey)) {
            $command[] = '--api-key';
            $command[] = $apiKey;
        }

        // Add optimized script parameters
        if ($useOptimized) {
            $maxConcurrent = config('services.python_translate.max_concurrent', 5);
            $command[] = '--max-concurrent';
            $command[] = (string) $maxConcurrent;

            if ($progressCallback !== null) {
                $command[] = '--progress';
            }

            Log::info('Using optimized Python translator with async processing', [
                'max_concurrent' => $maxConcurrent,
            ]);
        }

        $process = new Process($command, base_path());
        $process->setTimeout(null);

        // If using optimized version with progress callback, stream output
        if ($useOptimized && $progressCallback !== null) {
            $process->start();

            foreach ($process as $type => $data) {
                if ($type === Process::OUT) {
                    $this->handleProgressOutput($data, $progressCallback);
                }
            }

            $process->wait();
        } else {
            $process->run();
        }

        if (!$process->isSuccessful()) {
            $context = [
                'command' => $process->getCommandLine(),
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
            ];

            Log::error('Python translation script failed', $context);

            throw new ProcessFailedException($process);
        }

        $stats = $this->extractStats($process->getOutput());

        return [
            'success' => true,
            'stats' => $stats,
        ];
    }

    /**
     * Processa linhas de progresso JSON do script otimizado.
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
     * Extrai dados de estatistica do stdout do script.
     *
     * @return array<string, mixed>
     */
    private function extractStats(string $stdout): array
    {
        $stdout = trim($stdout);

        if ($stdout === '') {
            return [];
        }

        $lines = preg_split("/\r\n|\n|\r/", $stdout);

        // Look for result line from optimized script or fallback to last line
        foreach (array_reverse($lines) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $decoded = json_decode($line, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                continue;
            }

            // Optimized script format
            if (isset($decoded['type']) && $decoded['type'] === 'result' && isset($decoded['stats'])) {
                return $decoded['stats'];
            }

            // Original script format
            if (isset($decoded['stats'])) {
                return $decoded['stats'];
            }
        }

        Log::warning('Nao foi possivel decodificar estatisticas do script Python', [
            'stdout' => $stdout,
        ]);

        return [];
    }
}
