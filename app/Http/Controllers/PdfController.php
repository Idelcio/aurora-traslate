<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PdfController extends Controller
{
    // Redireciona para o dashboard (antiga página de upload foi removida)
    public function showUploadForm()
    {
        return redirect()->route('dashboard');
    }

    // Processa o upload do PDF e inicia a tradução
    public function upload(Request $request)
    {
        // Aumenta o timeout para PDFs grandes
        set_time_limit(0); // Sem limite de tempo
        ini_set('max_execution_time', '0');

        $allowedSourceLanguages = config('translation.source_languages', []);
        $allowedTargetLanguages = config('translation.target_languages', []);

        // Valida o upload
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:51200', // max 50MB
            'source_language' => [
                'nullable',
                'string',
                'max:10',
                Rule::in($allowedSourceLanguages),
            ],
            'target_language' => [
                'required',
                'string',
                'max:10',
                Rule::in($allowedTargetLanguages),
            ],
            'max_pages' => 'nullable|integer|min:1|max:1000',
        ]);

        $user = auth()->user();
        $subscription = $user->activeSubscription;

        // Verifica se o usuário tem plano ativo
        if (!$subscription || !$subscription->isActive()) {
            return back()->with('error', 'Você precisa de um plano ativo para traduzir livros.');
        }

        // Obtém o arquivo PDF
        $pdf = $request->file('pdf');
        $filename = time() . '_' . $pdf->getClientOriginalName();

        // Salva o PDF original
        $path = $pdf->storeAs('pdfs/originals', $filename, 'public');
        $fullPdfPath = storage_path('app/public/' . $path);

        // Conta o número real de páginas do PDF
        $pageCount = $this->countPdfPages($fullPdfPath);

        if ($pageCount === false) {
            Storage::disk('public')->delete($path);
            return back()->with('error', 'Não foi possível ler o arquivo PDF. Verifique se o arquivo está corrompido.');
        }

        // Verifica se o usuário pode fazer upload com base no plano
        if (!$user->canUploadBook($pageCount)) {
            Storage::disk('public')->delete($path);
            return back()->with('error', "Este livro tem {$pageCount} páginas, mas seu plano permite no máximo {$subscription->plan->max_pages} páginas.");
        }

        // Cria o registro do livro
        $book = $user->books()->create([
            'title' => pathinfo($pdf->getClientOriginalName(), PATHINFO_FILENAME),
            'original_filename' => $pdf->getClientOriginalName(),
            'pdf_path' => $path,
            'source_language' => $request->source_language ?? config('translation.defaults.source_language', 'auto'),
            'target_language' => $request->target_language,
            'total_pages' => $pageCount,
            'max_pages' => $request->max_pages,
            'status' => 'processing',
        ]);

        // Dispara a tradução em background usando um job
        \App\Jobs\TranslatePdfJob::dispatch($book);

        return redirect()->route('dashboard')->with('success', 'Upload realizado com sucesso! A tradução está sendo processada.');
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

    /**
     * Conta o número de páginas de um PDF usando Node.js
     */
    private function countPdfPages($pdfPath)
    {
        $scriptPath = base_path('scripts/countPdfPages.cjs');

        $command = sprintf(
            'node "%s" "%s" 2>&1',
            $scriptPath,
            $pdfPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            \Log::error('Erro ao contar páginas do PDF', [
                'path' => $pdfPath,
                'output' => implode("\n", $output)
            ]);
            return false;
        }

        $pageCount = intval(trim($output[0]));

        if ($pageCount <= 0) {
            \Log::error('Contagem de páginas inválida', [
                'path' => $pdfPath,
                'pageCount' => $pageCount
            ]);
            return false;
        }

        return $pageCount;
    }
}
