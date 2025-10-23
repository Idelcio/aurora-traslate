<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\Pdf\PythonPdfService;

$service = new PythonPdfService();

$pdfPath = 'storage/app/public/pdfs/originals/1761171459_Curriculo.pdf';
$outputPath = 'storage/app/public/pdfs/translated/test_output.pdf';

echo "Testing full Python PDF pipeline...\n";
echo "Input: $pdfPath\n";
echo "Output: $outputPath\n\n";

try {
    $result = $service->translatePdf(
        $pdfPath,
        $outputPath,
        'en', // target
        'pt' // source
    );
    
    echo "\n✓ SUCCESS!\n";
    print_r($result);
    
} catch (\Exception $e) {
    echo "\n✗ ERROR:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
