<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'source_text',
        'translated_text',
        'source_language_id',
        'target_language_id',
        'region_id',
        'user_id',
        'translation_method',
        'context',
        'rating',
        'feedback',
        'contains_idioms',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contains_idioms' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * Get the user who requested this translation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source language of this translation.
     */
    public function sourceLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'source_language_id');
    }

    /**
     * Get the target language of this translation.
     */
    public function targetLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'target_language_id');
    }

    /**
     * Get the region this translation is specific to, if any.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
