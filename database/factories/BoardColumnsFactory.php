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
            'column_index' => $this->faker->unique()->numberBetween(0, 100),
            'label' => fake()->word(), // Changed from 'label' to match your test
            'position' => fake()->numberBetween(0, 10),
            'is_visible' => true,
            'sort_config' => [],
        ];
    }
}
