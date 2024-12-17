<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController;
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
| Aqui você pode registrar as rotas web para sua aplicação. Todas as
| rotas são carregadas pelo RouteServiceProvider e serão atribuídas
| ao grupo middleware "web".
*/

// Rota para a página inicial
Route::get('/', function () {
    return view('auth.login');
});

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
    });

    // Rota para comandos
    Route::get('/comandos', [ComandosController::class, 'index'])->name('comandos.modal');
});

// Incluindo as rotas de autenticação
require __DIR__ . '/auth.php';
