<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$bookId = $argv[1] ?? null;

if (!$bookId) {
    echo "Uso: php export_book_data.php <book_id>\n";
    exit(1);
}

$book = DB::table('books')->where('id', $bookId)->first();

if (!$book) {
    echo "Livro não encontrado\n";
    exit(1);
}

$translated = DB::table('translated_texts')
    ->where('book_id', $bookId)
    ->orderBy('page_number')
    ->get();

$pages = [];

foreach ($translated as $t) {
    if (!isset($pages[$t->page_number])) {
        $pages[$t->page_number] = [
            'page' => $t->page_number,
            'textItems' => []
        ];
    }

    $pages[$t->page_number]['textItems'][] = [
        'originalText' => $t->original_text,
        'translatedText' => $t->translated_text
    ];
}

$data = [
    'pages' => array_values($pages)
];

$outputPath = __DIR__ . '/../storage/app/public/pdfs/data/book_' . $bookId . '_translated.json';
@mkdir(dirname($outputPath), 0755, true);

file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "JSON criado com " . count($pages) . " páginas\n";
echo "Arquivo: " . $outputPath . "\n";
echo "Total de items: " . count($translated) . "\n";
