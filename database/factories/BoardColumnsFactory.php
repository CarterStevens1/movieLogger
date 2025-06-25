<?php

namespace Database\Factories;

use App\Models\Board;
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
            'column_index' => 0,
            'label' => fake()->unique()->word,
            'position' => 0,
            'is_visible' => true,
            'sort_config' => [],
        ];
    }
}
