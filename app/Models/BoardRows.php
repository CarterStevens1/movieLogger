<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardRows extends Model
{
    /** @use HasFactory<\Database\Factories\BoardRowsFactory> */
    use HasFactory;

    protected $fillable = [
        'board_id',
        'row_index',
        'label',
        'position',
        'is_visible'
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public static function generateLabel($index)
    {
        return (string) $index; // Simple numeric label
    }

    public static function createInitialRows($boardId, $count = 50)
    {
        for ($i = 1; $i <= $count; $i++) { // Start from 1 for row numbering
            self::create([
                'board_id' => $boardId,
                'row_index' => $i,
                'label' => self::generateLabel($i),
                'position' => $i - 1 // 0-based position for ordering
            ]);
        }
    }
}
