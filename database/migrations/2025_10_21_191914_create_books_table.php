<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('original_filename');
            $table->string('pdf_path'); // caminho do PDF original
            $table->string('translated_pdf_path')->nullable(); // caminho do PDF traduzido
            $table->string('audio_path')->nullable(); // caminho do áudio (TTS)
            $table->integer('total_pages');
            $table->enum('status', ['uploaded', 'processing', 'translated', 'error'])->default('uploaded');
            $table->string('source_language', 10)->default('en');
            $table->string('target_language', 10)->default('pt_BR');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
