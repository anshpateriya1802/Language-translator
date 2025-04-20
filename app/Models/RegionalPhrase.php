<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionalPhrase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'source_phrase',
        'translation',
        'source_language_id',
        'target_language_id',
        'region_id',
        'context',
        'is_idiom',
        'is_slang',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_idiom' => 'boolean',
        'is_slang' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the source language of this regional phrase.
     */
    public function sourceLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'source_language_id');
    }

    /**
     * Get the target language of this regional phrase.
     */
    public function targetLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'target_language_id');
    }

    /**
     * Get the region this phrase belongs to.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
