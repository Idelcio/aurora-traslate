<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Pdf; // Importa o modelo Pdf

class PdfController extends Controller
{
    // Exibe o formulário de upload
    public function showUploadForm()
    {
        return view('pdf.upload_view'); // Atualize para o nome correto do arquivo
    }



    // Processa o upload do PDF
    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:2048',
        ]);

        // Salva o arquivo PDF no storage
        $path = $request->file('pdf')->store('pdfs');

        // Salva as informações do PDF no banco de dados
        Pdf::create([
            'filename' => basename($path),
        ]);

        // Redireciona para a visualização do PDF
        return redirect()->route('pdf.upload.form', ['filename' => basename($path)]);
    }

    // Exibe a visualização do PDF
    public function show($filename)
    {
        return view('pdf.view', ['filename' => $filename]);
    }

    // Lista todos os PDFs
    public function index()
    {
        $pdfs = Pdf::all();
        return view('pdf.index', compact('pdfs'));
    }

    // Método para download do PDF
    public function download($filename)
    {
        $path = storage_path('app/pdfs/' . $filename);

        if (!file_exists($path)) {
            abort(404); // Se o arquivo não existir, retorna erro 404
        }

        return response()->download($path);
    }
}
