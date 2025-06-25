<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardColumns;
use App\Models\BoardRows;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BoardCells>
 */
class BoardCellsFactory extends Factory
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
            'board_row_id' => BoardRows::factory(),
            'board_column_id' => BoardColumns::factory(),
            'value' => fake()->unique()->word,
            'tag_config' => [],
        ];
    }
}
