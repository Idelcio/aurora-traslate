<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsOfUse extends Model
{
    use HasFactory;

    /**
     * O nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'terms_of_uses';  // Define a tabela caso o nome seja personalizado

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'language'];  // Inclui a coluna 'language'
}
