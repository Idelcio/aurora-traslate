<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = app(\App\Services\GoogleTranslateService::class);

echo "Testing PHP translation...\n";

$texts = ['Programador Fullstack', 'Formação Acadêmica', 'Porto Alegre - RS - Brasil'];
$result = $service->translateTextsOptimized($texts, 'en', 'pt-BR');

echo "\nResults:\n";
foreach ($texts as $text) {
    echo "  '$text' => '{$result[$text]}'\n";
}
