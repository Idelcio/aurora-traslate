<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController; // Importação do PdfController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrawBallController;
use App\Http\Controllers\ImageController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\AnnotationController;
use App\Http\Controllers\TermosController;

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

    // Rota para Termos de Segurança
    Route::get('/termos-seguranca', [TermosController::class, 'showTermosSeguranca'])->name('termos.seguranca');

    // Rota para Política de Privacidade
    Route::get('/politica-privacidade', [TermosController::class, 'showPoliticaPrivacidade'])->name('politica.privacidade');
});

// Incluindo as rotas de autenticação
require __DIR__ . '/auth.php';
