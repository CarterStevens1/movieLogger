<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardRows;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoardRows>
 */
class BoardRowsFactory extends Factory
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
            'row_index' => function (array $attributes) {
                // Generate unique row_index for this board
                $maxIndex = BoardRows::where('board_id', $attributes['board_id'])
                    ->max('row_index') ?? 0;
                return $maxIndex + 1;
            },
            'label' => fake()->unique()->word,
            'position' => fake()->numberBetween(0, 10),
            'is_visible' => true,
        ];
    }
}
