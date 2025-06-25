<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Board extends Model
{
    /** @use HasFactory<\Database\Factories\BoardsFactory> */
    use HasFactory;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'tags',
    ];


    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'board_user');
    }

    // Columns
    public function columns()
    {
        return $this->hasMany(BoardColumns::class)->orderBy('position');
    }

    // Rows
    public function rows()
    {
        return $this->hasMany(BoardRows::class)->orderBy('position');
    }

    // Update the boot method to create both columns and rows
    protected static function booted()
    {
        static::created(function ($board) {
            BoardColumns::createInitialColumns($board->id);
            BoardRows::createInitialRows($board->id);
        });
    }
}
