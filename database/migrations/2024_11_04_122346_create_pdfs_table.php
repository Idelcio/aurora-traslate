<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfsTable extends Migration
{
    public function up()
    {
        Schema::create('pdfs', function (Blueprint $table) {
            $table->id(); // ID da tabela
            $table->string('filename'); // Nome do arquivo PDF
            $table->timestamps(); // Timestamps para created_at e updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('pdfs'); // Remove a tabela se a migration for revertida
    }
}
