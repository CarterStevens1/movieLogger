<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BoardCells extends Model
{
    /** @use HasFactory<\Database\Factories\BoardCellsFactory> */
    use HasFactory;

    public function boardColumns(): BelongsToMany
    {
        return $this->belongsToMany(BoardColumns::class);
    }

    public function boardRows(): BelongsToMany
    {
        return $this->belongsToMany(BoardRows::class);
    }
}
