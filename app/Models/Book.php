<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'original_filename',
        'pdf_path',
        'translated_pdf_path',
        'audio_path',
        'total_pages',
        'start_page',
        'end_page',
        'status',
        'source_language',
        'target_language',
        'error_message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTranslated(): bool
    {
        return $this->status === 'translated';
    }

    public function hasAudio(): bool
    {
        return !empty($this->audio_path);
    }
}
