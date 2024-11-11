<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PdfController; // Importação do PdfController
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrawBallController;
use App\Http\Controllers\ImageController;

/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
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



    // Rota para exibir o formulário de upload
    Route::get('/pdf/upload', [PdfController::class, 'showUploadForm'])->name('pdf.upload');

    // Rota para processar o upload do PDF
    Route::post('/pdf/upload', [PdfController::class, 'upload'])->name('pdf.upload.post');

    // Rota para visualizar o PDF
    Route::get('/pdf/view/{filename}', [PdfController::class, 'show'])->name('pdf.show');
});

// Incluindo as rotas de autenticação
require __DIR__ . '/auth.php';
