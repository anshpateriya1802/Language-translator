<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'native_name',
        'is_active',
    ];

    /**
     * Get the regions associated with this language.
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    /**
     * Get translations where this is the source language.
     */
    public function sourceTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'source_language_id');
    }

    /**
     * Get translations where this is the target language.
     */
    public function targetTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'target_language_id');
    }

    /**
     * Get regional phrases where this is the source language.
     */
    public function sourceRegionalPhrases(): HasMany
    {
        return $this->hasMany(RegionalPhrase::class, 'source_language_id');
    }

    /**
     * Get regional phrases where this is the target language.
     */
    public function targetRegionalPhrases(): HasMany
    {
        return $this->hasMany(RegionalPhrase::class, 'target_language_id');
    }
}
