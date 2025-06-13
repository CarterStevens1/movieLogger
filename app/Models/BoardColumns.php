<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardColumns extends Model
{
    /** @use HasFactory<\Database\Factories\BoardColumnsFactory> */
    use HasFactory;

    public function board(): BelongsToMany
    {
        return $this->belongsToMany(Board::class);
    }

    public function boardCells(): HasMany
    {
        return $this->hasMany(BoardCells::class);
    }
}
