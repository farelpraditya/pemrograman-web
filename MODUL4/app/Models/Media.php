<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'title',
        'genre',
        'year',
        'category',
        'rating',
        'total_parts',
        'creator',
    ];

    protected $casts = [
        'year' => 'integer',
        'rating' => 'float',
        'total_parts' => 'integer',
    ];

    public const CATEGORIES = ['Anime', 'Drama', 'Manga', 'Light Novel'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')->withTimestamps();
    }

    protected function type(): Attribute
    {
        return Attribute::get(
            fn () => in_array($this->category, ['Anime', 'Drama']) ? 'video' : 'reading'
        );
    }

    protected function unit(): Attribute
    {
        return Attribute::get(fn () => match ($this->category) {
            'Anime', 'Drama' => 'episode',
            'Manga' => 'chapter',
            'Light Novel' => 'volume',
            default => null,
        });
    }

    protected function detail(): Attribute
    {
        return Attribute::get(function () {
            if ($this->type === 'video') {
                return "{$this->title} ({$this->category}) produksi {$this->creator} dengan {$this->total_parts} episode. Siap ditonton.";
            }

            return "{$this->title} ({$this->category}) karya {$this->creator} memiliki {$this->total_parts} {$this->unit}. Siap dibaca.";
        });
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $keyword
            ? $query->where('title', 'like', "%{$keyword}%")
            : $query;
    }

    public function scopeOfCategory(Builder $query, ?string $category): Builder
    {
        return $category
            ? $query->where('category', $category)
            : $query;
    }
}
