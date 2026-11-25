<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_user')->withTimestamps();
    }
}
