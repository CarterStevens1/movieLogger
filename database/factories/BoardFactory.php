<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Add faker random string to name
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->word,
            'description' => fake()->paragraph,
            'tags' => fake()->words(3, true),
            //
        ];
    }
}
