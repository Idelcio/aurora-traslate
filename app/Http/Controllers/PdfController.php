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

    // Processa o upload do PDF
    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:2048', // Valida o arquivo PDF
        ]);

        // Salva o arquivo PDF no storage
        $path = $request->file('pdf')->store('pdfs');

        // Redireciona para a mesma página com o nome do arquivo
        return redirect()->route('pdf.upload')
            ->with('success', 'PDF carregado com sucesso! Arquivo: ' . basename($path))
            ->with('pdf_filename', basename($path)); // Passa o nome do arquivo para a sessão
    }

    // Exibe o PDF
    public function show($filename)
    {
        $filePath = storage_path('app/pdfs/' . $filename);

        if (!file_exists($filePath)) {
            abort(404); // Se o arquivo não existir, retorna erro 404
        }

        return response()->file($filePath);
    }
}
