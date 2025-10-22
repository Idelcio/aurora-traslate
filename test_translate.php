<?php

/**
 * Script de teste para verificar a tradução de PDFs
 * Execute com: php test_translate.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Services\GoogleTranslateService;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Teste de Tradução de PDF ===\n\n";

// Teste 1: Verificar se Node.js está disponível
echo "1. Verificando Node.js...\n";
exec('node --version 2>&1', $output, $returnCode);

if ($returnCode !== 0) {
    echo "   ❌ ERRO: Node.js não está instalado ou não está no PATH\n";
    echo "   Por favor, instale o Node.js: https://nodejs.org/\n";
    exit(1);
}

echo "   ✓ Node.js encontrado: " . trim($output[0]) . "\n\n";

// Teste 2: Verificar se os scripts Node.js existem
echo "2. Verificando scripts Node.js...\n";

$extractScript = base_path('scripts/extractPdfText.js');
$rebuildScript = base_path('scripts/rebuildPdfWithTranslation.js');

if (!file_exists($extractScript)) {
    echo "   ❌ ERRO: Script de extração não encontrado em: $extractScript\n";
    exit(1);
}

if (!file_exists($rebuildScript)) {
    echo "   ❌ ERRO: Script de reconstrução não encontrado em: $rebuildScript\n";
    exit(1);
}

echo "   ✓ Script de extração encontrado\n";
echo "   ✓ Script de reconstrução encontrado\n\n";

// Teste 3: Verificar instalação dos pacotes Node.js
echo "3. Verificando pacotes Node.js...\n";

$packageJson = base_path('package.json');
if (!file_exists($packageJson)) {
    echo "   ❌ ERRO: package.json não encontrado\n";
    exit(1);
}

$requiredPackages = ['pdf-lib', 'pdfjs-dist', 'fontkit'];
$nodeModules = base_path('node_modules');

foreach ($requiredPackages as $package) {
    $packagePath = $nodeModules . '/' . $package;
    if (!is_dir($packagePath)) {
        echo "   ❌ ERRO: Pacote '$package' não instalado\n";
        echo "   Execute: npm install\n";
        exit(1);
    }
    echo "   ✓ $package instalado\n";
}

echo "\n";

// Teste 4: Verificar Google Translate API
echo "4. Verificando Google Translate API...\n";

$apiKey = config('services.google_translate.api_key');

if (empty($apiKey)) {
    echo "   ⚠️  AVISO: Google Translate API key não configurada no .env\n";
    echo "   Configure GOOGLE_TRANSLATE_API_KEY no arquivo .env\n";
} else {
    echo "   ✓ API Key configurada\n";

    // Teste simples de tradução
    try {
        $translateService = app(GoogleTranslateService::class);
        $result = $translateService->translate('Hello', 'pt');

        if ($result['success']) {
            echo "   ✓ Tradução funcionando: 'Hello' -> '" . $result['translatedText'] . "'\n";
        } else {
            echo "   ❌ ERRO ao traduzir: " . $result['error'] . "\n";
        }
    } catch (Exception $e) {
        echo "   ❌ ERRO: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Teste 5: Verificar diretórios de armazenamento
echo "5. Verificando diretórios de armazenamento...\n";

$requiredDirs = [
    storage_path('app/public/pdfs/temp'),
    storage_path('app/public/pdfs/translated'),
];

foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "   ✓ Diretório criado: $dir\n";
    } else {
        echo "   ✓ Diretório existe: $dir\n";
    }

    if (!is_writable($dir)) {
        echo "   ❌ ERRO: Diretório não possui permissão de escrita: $dir\n";
        exit(1);
    }
}

echo "\n";

echo "=== Resumo ===\n";
echo "✓ Sistema pronto para traduzir PDFs!\n";
echo "\nPróximos passos:\n";
echo "1. Certifique-se de que o servidor Laravel está rodando (php artisan serve)\n";
echo "2. Faça login no sistema\n";
echo "3. Acesse o Dashboard e faça upload de um PDF para traduzir\n";
echo "\nObservações importantes:\n";
echo "- O PDF será traduzido usando fonte Helvetica (legível)\n";
echo "- O texto será sempre em preto para facilitar a leitura\n";
echo "- O número de páginas do PDF traduzido será igual ao original\n";
