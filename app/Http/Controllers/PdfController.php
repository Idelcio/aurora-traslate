<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    // Exibe o formulário de upload
    public function showUploadForm()
    {
        return view('pdf.pdf_upload');
    }

    // Processa o upload do PDF e exibe na mesma página
    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:2048', // Validação do arquivo PDF
        ]);

        // Salva o arquivo PDF no storage
        $path = $request->file('pdf')->store('pdfs');
        $filename = basename($path);

        // Retorna para a mesma página com o nome do arquivo
        return view('pdf.pdf_upload', ['pdf_filename' => $filename]);
    }

    // Exibe o PDF para renderização com PDF.js
    public function show($filename)
    {
        $filePath = storage_path('app/pdfs/' . $filename);

        if (!file_exists($filePath)) {
            abort(404); // Erro 404 se o arquivo não existir
        }

        return response()->file($filePath);
    }
}
