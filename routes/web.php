<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PdfTranslateController;
use App\Http\Controllers\BookTranslateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrawBallController;
use App\Http\Controllers\ImageController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\AnnotationController;
use App\Http\Controllers\TermsOfUseController;
use App\Http\Controllers\ComandosController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UserPlanController;
use Illuminate\Support\Facades\Auth;

/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
| Aqui você pode registrar as rotas web para sua aplicação. Todas as
| rotas são carregadas pelo RouteServiceProvider e serão atribuídas
| ao grupo middleware "web".
*/

// Rota para a página inicial
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// Rota para o dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/terms/{id}', [TermsOfUseController::class, 'show'])->name('terms.show');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Rotas para o gerenciamento de perfil do usuário
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Rotas para o gerenciamento de usuários
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/index', [UserController::class, 'index'])->name('index');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
    });

    // Rotas de CRUD para Termos de Uso
    Route::prefix('terms')->name('terms.')->group(function () {
        Route::get('/', [TermsOfUseController::class, 'index'])->name('index');
        Route::get('/create', [TermsOfUseController::class, 'create'])->name('create');
        Route::post('/', [TermsOfUseController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TermsOfUseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TermsOfUseController::class, 'update'])->name('update');
        Route::delete('/{id}', [TermsOfUseController::class, 'destroy'])->name('destroy');
    });

    // Rotas para o PDF
    Route::prefix('pdf')->name('pdf.')->group(function () {
        Route::get('/upload', [PdfController::class, 'showUploadForm'])->name('upload');
        Route::post('/upload', [PdfController::class, 'upload'])->name('upload.post');
        Route::get('/view/{filename}', [PdfController::class, 'show'])->name('show');
        Route::post('/save', [PdfController::class, 'savePdf'])->name('save');
        Route::post('/save-annotations', [PdfController::class, 'savePdfWithAnnotations'])->name('saveAnnotations');
        Route::post('/save-with-annotations', [PdfController::class, 'savePdfWithAnnotations'])->name('save_with_annotations');

        // Rotas para tradução de PDF
        Route::post('/translate', [PdfTranslateController::class, 'translate'])->name('translate');
        Route::get('/translate/download/{filename}', [PdfTranslateController::class, 'download'])->name('translate.download');
    });

    // Rotas para livros
    Route::prefix('books')->name('books.')->group(function () {
        // Inicia tradução de um livro
        Route::post('/{book}/translate', [BookTranslateController::class, 'translate'])->name('translate');

        // Download de livro traduzido
        Route::get('/{book}/download', function ($bookId) {
            $book = \App\Models\Book::findOrFail($bookId);

            // Verifica se o livro pertence ao usuário autenticado
            if ($book->user_id !== auth()->id()) {
                abort(403, 'Você não tem permissão para baixar este livro.');
            }

            // Verifica se o livro foi traduzido
            if ($book->status !== 'translated' || !$book->translated_pdf_path) {
                abort(404, 'Este livro ainda não foi traduzido.');
            }

            $filePath = storage_path('app/public/' . $book->translated_pdf_path);

            if (!file_exists($filePath)) {
                abort(404, 'Arquivo traduzido não encontrado.');
            }

            $downloadName = trim($book->title) !== ''
                ? $book->title . ' (traduzido).pdf'
                : 'Documento Traduzido.pdf';

            return response()->download($filePath, $downloadName);
        })->name('download');
    });

    // Rota para comandos
    Route::get('/comandos', [ComandosController::class, 'index'])->name('comandos.modal');

    // Rota para alert
    Route::get('/translations/pdf_upload', function () {
        return response()->json([
            'unsaved_changes' => __('pdf_upload.unsaved_changes'),
        ]);
    });

    Route::get('/translations/pdf_navigation', function () {
        return response()->json([
            'first_page_alert' => __('pdf_upload.first_page_alert'),
            'last_page_alert' => __('pdf_upload.last_page_alert'),
        ]);
    });
    Route::get('/translations/pdf_save', function () {
        return response()->json([
            'save_pdf_error' => __('pdf_upload.save_pdf_error'),
            'fontkit_missing' => __('pdf_upload.fontkit_missing'),
            'pdf_saved_success' => __('pdf_upload.pdf_saved_success'),
            'watermark_text' => __('pdf_upload.watermark_text'),
        ]);
    });
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::patch('/users/{user}/plan', [UserPlanController::class, 'update'])->name('users.plan.update');
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::patch('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
});

// Incluindo as rotas de autenticação
require __DIR__ . '/auth.php';
