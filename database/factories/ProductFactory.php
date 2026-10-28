<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->randomElement(['Bruschetta tradicional', 'Vol-au-vent de frango', 'Salada Caesar', 'Filé mignon ao molho de mostarda', 'Arroz à grega', 'Torta de chocolate']),
            'descricao' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 2, 50),
            'status' => true,
        ];
    }

    /**
     * Indicate that the product is hidden from the public menu.
     */
    public function inativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
