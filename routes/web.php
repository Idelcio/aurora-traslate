<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //User
    Route::get('/users-index', [UserController::class, 'index'])->name('users.index');
    Route::get('/users-edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/edit-update/{id}', [UserController::class, 'update'])->name('users.update');

    //pdf-editor



    // Rota para mostrar o formulário de upload
    Route::get('/pdf/upload', [PdfController::class, 'showUploadForm'])->name('pdf.upload');
    Route::post('/pdf/upload', [PdfController::class, 'upload'])->name('pdf.upload.post');

    // Rota para visualizar um PDF
    Route::get('/pdf/view/{filename}', [PdfController::class, 'show'])->name('pdf.view');

    // Rota para listar todos os PDFs com um nome diferente
    Route::get('/pdf/list', [PdfController::class, 'listPdfs'])->name('pdf.list'); // Nome diferente para a lista de PDFs

});

require __DIR__ . '/auth.php';
