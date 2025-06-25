<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardColumns extends Model
{
    /** @use HasFactory<\Database\Factories\BoardColumnsFactory> */
    use HasFactory;

    protected $fillable = [
        'board_id',
        'column_index',
        'label',
        'position',
        'is_visible',
        'sort_config'
    ];

    protected $casts = [
        'sort_config' => 'array',
        'is_visible' => 'boolean'
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
    // Generate labels for columns a-z aa-zz aaa-zzz etc.
    public static function generateLabel($index)
    {
        $result = '';
        while ($index >= 0) {
            $result = chr(65 + ($index % 26)) . $result;
            $index = intval($index / 26) - 1;
        }
        return $result;
    }
    // Initially create 20 columns on board creation
    public static function createInitialColumns($boardId, $count = 20)
    {
        for ($i = 0; $i < $count; $i++) {
            self::create([
                'board_id' => $boardId,
                'column_index' => $i,
                'label' => self::generateLabel($i),
                'position' => $i
            ]);
        }
    }
}
