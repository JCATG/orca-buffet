<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'data_evento' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'local_evento' => fake()->streetAddress(),
            'tipo_evento' => fake()->randomElement(['Casamento', 'Aniversário', 'Formatura', 'Corporativo', 'Batizado']),
            'convidados' => fake()->numberBetween(30, 300),
            'status' => OrderStatus::Pendente,
            'observacoes' => fake()->optional()->sentence(),
            'total' => null,
        ];
    }

    /**
     * Indicate that the quote has already been sent to the customer.
     */
    public function enviado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::Enviado,
            'enviado_em' => now(),
        ]);
    }
}
