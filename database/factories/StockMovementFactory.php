<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'quantity_change' => $this->faker->numberBetween(-10, 10),
            'count' => $this->faker->numberBetween(1, 50),
            'type' => $this->faker->randomElement(['order_create', 'order_cancel', 'manual']),
            'description' => $this->faker->sentence(),
        ];
    }
}
