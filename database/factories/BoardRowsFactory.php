<?php

namespace Database\Factories;

use App\Models\Board;
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
            'row_index' => $this->faker->unique()->numberBetween(0, 100),
            'label' => fake()->unique()->word,
            'position' => fake()->numberBetween(0, 10),
            'is_visible' => true,
        ];
    }
}
