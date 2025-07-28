<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unit_price = $this->faker->randomFloat(2, 10, 200);
        return [
            'customer_id' => Customer::factory(),
            'saleable_id' => null, // Polymorphic, set to null or configure later
            'saleable_type' => null, // Polymorphic, set to null or configure later
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_amount' => $quantity * $unit_price,
            'paid_amount' => $this->faker->randomFloat(2, 0, $quantity * $unit_price),
            'status' => $this->faker->randomElement(['pending', 'paid', 'partially_paid', 'overdue']),
            'sale_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'product_variant' => $this->faker->optional()->word(),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}