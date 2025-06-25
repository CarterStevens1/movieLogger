<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardCells extends Model
{
    /** @use HasFactory<\Database\Factories\BoardCellsFactory> */
    use HasFactory;

    protected $fillable = [
        'board_id',
        'board_row_id',
        'board_column_id',
        'value',
        'tag_config'
    ];

    protected $casts = [
        'tag_config' => 'array'
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function boardRow(): BelongsTo
    {
        return $this->belongsTo(BoardRows::class);
    }

    public function boardColumn(): BelongsTo
    {
        return $this->belongsTo(BoardColumns::class);
    }
}
