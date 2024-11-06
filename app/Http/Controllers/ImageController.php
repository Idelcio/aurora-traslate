<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // Exibe o formulário de upload de imagem
    public function showUploadForm()
    {
        return view('image.upload');
    }

    // Processa o upload da imagem
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Armazenando o arquivo temporariamente na pasta 'public'
        $path = $request->file('image')->storeAs('public/images', $request->file('image')->getClientOriginalName());

        // Retorna o nome da imagem e a exibe
        return redirect()->route('image.view', ['filename' => $request->file('image')->getClientOriginalName()]);
    }

    // Exibe a imagem carregada
    public function viewImage($filename)
    {
        // Verifique se o arquivo existe na pasta pública
        $filePath = public_path('storage/images/' . $filename);

        if (!file_exists($filePath)) {
            abort(404); // Se o arquivo não existir, retorna erro 404
        }

        // Retorna a view com o nome do arquivo
        return view('image.view', ['filename' => $filename]);
    }
}
