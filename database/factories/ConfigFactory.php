<?php

namespace Database\Factories;

use App\Models\Config;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Config>
 */
class ConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chave' => fake()->unique()->slug(2),
            'valor' => fake()->sentence(),
        ];
    }
}
