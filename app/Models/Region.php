<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
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
        'language_id',
        'country',
        'description',
        'is_active',
    ];

    /**
     * Get the language that this region belongs to.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the regional phrases associated with this region.
     */
    public function regionalPhrases(): HasMany
    {
        return $this->hasMany(RegionalPhrase::class);
    }

    /**
     * Get the translations associated with this region.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}
