<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController; // Importação do PdfController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrawBallController;
use App\Http\Controllers\ImageController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\AnnotationController;
use App\Http\Controllers\TermsOfUseController;
use App\Http\Controllers\ComandosController;



/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
|
| Aqui você pode registrar as rotas web para sua aplicação. Todas as
| rotas são carregadas pelo RouteServiceProvider e serão atribuídas
| ao grupo middleware "web".
|
*/

// Rota para a página inicial
Route::get('/', function () {
    return view('auth.login');
});

// Rotas públicas para Termos de Uso
Route::get('/terms', [TermsOfUseController::class, 'index'])->name('terms.index');
Route::get('/terms/{id}', [TermsOfUseController::class, 'show'])->name('terms.show');
// Rota para o dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Rotas para o gerenciamento de perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas para o gerenciamento de usuários
    Route::get('/users-index', [UserController::class, 'index'])->name('users.index');
    Route::get('/users-edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/edit-update/{id}', [UserController::class, 'update'])->name('users.update');

    // Rotas para o PDF
    // Rota para exibir o formulário de upload
    Route::get('/pdf/upload', [PdfController::class, 'showUploadForm'])->name('pdf.upload');

    // Rota para processar o upload do PDF
    Route::post('/pdf/upload', [PdfController::class, 'upload'])->name('pdf.upload.post');

    // Rota para visualizar o PDF
    Route::get('/pdf/view/{filename}', [PdfController::class, 'show'])->name('pdf.show');

    // Rota para salvar o PDF com as anotações
    Route::post('/pdf/save-annotations', [PdfController::class, 'savePdfWithAnnotations'])->name('pdf.saveAnnotations');
    Route::post('/pdf/save', [PdfController::class, 'savePdf'])->name('pdf.save');
    Route::post('/pdf/save-with-annotations', [PdfController::class, 'savePdfWithAnnotations'])->name('pdf.save_with_annotations');


    // Rotas protegidas por autenticação
    Route::middleware('auth')->group(function () {

        // Dashboard protegido
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');


        // Rotas de CRUD para Termos de Uso
        Route::prefix('terms')->name('terms.')->group(function () {
            Route::get('/create', [TermsOfUseController::class, 'create'])->name('create');
            Route::post('/', [TermsOfUseController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TermsOfUseController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TermsOfUseController::class, 'update'])->name('update');
            Route::delete('/{id}', [TermsOfUseController::class, 'destroy'])->name('destroy');
        });
    });

    // comandos

    Route::get('/comandos', [ComandosController::class, 'index'])->name('comandos.modal');
});

// Incluindo as rotas de autenticação
require __DIR__ . '/auth.php';
