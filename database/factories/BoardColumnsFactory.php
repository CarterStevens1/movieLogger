<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardColumns;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoardColumns>
 */
class BoardColumnsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'board_id' => Board::factory(),
            'column_index' => function (array $attributes) {
                // Generate unique row_index for this board
                $maxIndex = BoardColumns::where('board_id', $attributes['board_id'])
                    ->max('column_index') ?? 0;
                return $maxIndex + 1;
            },
            'label' => fake()->word(), // Changed from 'label' to match your test
            'position' => fake()->numberBetween(0, 10),
            'is_visible' => true,
            'sort_config' => [],
        ];
    }
}
