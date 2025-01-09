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
        Schema::table('terms_of_uses', function (Blueprint $table) {
            $table->string('language', 5)->default('en'); // Adiciona a coluna de idioma com um valor padrão
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_of_uses', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
};
