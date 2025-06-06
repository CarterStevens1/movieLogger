<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class boards extends Model
{
    /** @use HasFactory<\Database\Factories\BoardsFactory> */
    use HasFactory;


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
