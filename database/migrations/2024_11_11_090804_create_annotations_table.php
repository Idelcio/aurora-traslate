<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pdf_id')->constrained()->onDelete('cascade'); // Associa ao PDF
            $table->float('x');
            $table->float('y');
            $table->integer('counter'); // Número da anotação
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annotations');
    }
};
