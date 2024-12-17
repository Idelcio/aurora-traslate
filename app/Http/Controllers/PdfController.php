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
        // Valida o upload, sem limite de tamanho
        $request->validate([
            'pdf' => 'required|mimes:pdf',
        ]);

        // Obtém o arquivo PDF
        $pdf = $request->file('pdf');

        // Obtém o nome original do arquivo
        $filename = $pdf->getClientOriginalName();

        // Salva o arquivo no diretório 'pdfs' sem modificar o nome original
        $pdf->storeAs('pdfs', $filename, 'public');

        // Define uma variável de sessão para indicar que o upload foi bem-sucedido
        session()->flash('pdf_uploaded', true);

        // Retorna para a mesma view, passando o nome do arquivo
        return view('pdf.pdf_upload', ['pdf_filename' => $filename]);
    }


    // Exibe o PDF para renderização com PDF.js
    public function show($filename)
    {
        $filePath = storage_path('app/public/pdfs/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado');
        }

        return response()->file($filePath);
    }



    // Salva o PDF com anotações (sem manipulação direta no PDF)
    public function savePdfWithAnnotations(Request $request)
    {
        // Validação das coordenadas e arquivo PDF
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
            'annotations' => 'required|array'
        ]);

        // Salva o PDF recebido no servidor
        $path = $request->file('pdf')->store('pdfs');
        $filename = basename($path);

        // Salva as anotações em um arquivo JSON
        $annotationsPath = 'pdfs/annotations_' . time() . '.json';
        Storage::put($annotationsPath, json_encode($request->input('annotations')));

        return response()->json([
            'message' => 'PDF e anotações salvos com sucesso!',
            'pdfPath' => $path,
            'annotationsPath' => $annotationsPath
        ], 200);
    }
}
