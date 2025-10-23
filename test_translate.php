<?php

/**
 * Quick health check for the PDF translation toolchain.
 * Execute with: php test_translate.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\GoogleTranslateService;
use Illuminate\Support\Str;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Diagnostico do Tradutor de PDF ===\n\n";

// Step 1: Node.js availability
echo "1. Verificando Node.js...\n";
runCheck('node --version', 'Node.js');

// Step 2: Python availability
echo "2. Verificando Python...\n";
$pythonBinary = config('services.python_translate.binary') ?? env('PYTHON_TRANSLATE_BINARY', 'python');
runCheck("{$pythonBinary} --version", "Python ({$pythonBinary})");

// Step 3: Required scripts
echo "3. Conferindo scripts obrigatorios...\n";
assertFileExists(base_path('scripts/extractPdfText.cjs'), 'extractPdfText.cjs');
assertFileExists(base_path('scripts/rebuildPdfWithTranslation.cjs'), 'rebuildPdfWithTranslation.cjs');
assertFileExists(base_path('scripts/rebuildSimplePdf.cjs'), 'rebuildSimplePdf.cjs');
assertFileExists(base_path('scripts/python/translate_and_format.py'), 'translate_and_format.py');
assertFileExists(base_path('scripts/python/requirements.txt'), 'requirements.txt (Python)');
echo "   OK\n\n";

// Step 4: Node dependencies
echo "4. Validando pacotes Node.js...\n";
assertFileExists(base_path('package.json'), 'package.json');
verifyDirectories([
    base_path('node_modules/pdf-lib') => 'pdf-lib',
    base_path('node_modules/pdfjs-dist') => 'pdfjs-dist',
    base_path('node_modules/fontkit') => 'fontkit',
]);
echo "   OK\n\n";

// Step 5: Python dependencies
echo "5. Verificando modulo requests para Python...\n";
runCheck("{$pythonBinary} -c \"import requests\" 2>&1", 'Modulo requests', false, function ($output) {
    echo "   ERRO: Nao foi possivel importar requests. Execute:\n";
    echo "         python -m pip install -r scripts/python/requirements.txt\n";
    echo "         (ajuste o comando conforme o ambiente)\n";
    exit(1);
});
echo "   OK\n\n";

// Step 6: Google Translate API key
echo "6. Checando Google Translate API...\n";
$apiKey = config('services.google_translate.api_key');

if (empty($apiKey)) {
    echo "   AVISO: variavel GOOGLE_TRANSLATE_API_KEY nao configurada no .env\n";
    echo "          Configurar a chave evita falhas de traducao.\n\n";
} else {
    echo "   API Key configurada\n";
    try {
        $translateService = app(GoogleTranslateService::class);
        $result = $translateService->translate('Hello', 'pt');
        if (($result['success'] ?? false) === true) {
            echo "   Traducoes API funcionando: Hello -> {$result['translatedText']}\n\n";
        } else {
            echo "   ERRO ao validar Google Translate: {$result['error']}\n\n";
        }
    } catch (\Throwable $throwable) {
        echo "   ERRO ao acessar Google Translate: {$throwable->getMessage()}\n\n";
    }
}

// Step 7: Storage directories
echo "7. Conferindo diretorios de armazenamento...\n";
foreach ([
    storage_path('app/public/pdfs/temp'),
    storage_path('app/public/pdfs/translated'),
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "   Diretorio criado: {$dir}\n";
    } else {
        echo "   Diretorio existente: {$dir}\n";
    }

    if (!is_writable($dir)) {
        echo "   ERRO: sem permissao de escrita em {$dir}\n";
        exit(1);
    }
}

echo "\n=== Diagnostico concluido ===\n";
echo "Tudo pronto para traduzir PDFs.\n";
echo "Lembre de rodar um npm install e python -m pip install -r scripts/python/requirements.txt se necessario.\n";

// ---------------------------------------------------------------------------

function runCheck(string $command, string $label, bool $exitOnFailure = true, ?callable $onFailure = null): void
{
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);

    if ($returnCode !== 0) {
        echo "   ERRO: {$label} nao detectado.\n";
        if ($onFailure) {
            $onFailure($output);
        }
        if ($exitOnFailure) {
            echo "   Saida do comando: " . implode("\n", $output) . "\n";
            exit(1);
        }
    } else {
        $version = trim($output[0] ?? '');
        echo "   {$label}: {$version}\n";
    }
}

function assertFileExists(string $path, string $label): void
{
    if (!file_exists($path)) {
        echo "   ERRO: {$label} nao encontrado em {$path}\n";
        exit(1);
    }
}

function verifyDirectories(array $map): void
{
    foreach ($map as $path => $label) {
        if (!is_dir($path)) {
            echo "   ERRO: pacote {$label} nao encontrado (verifique npm install)\n";
            exit(1);
        }
    }
}
