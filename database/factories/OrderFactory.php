<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            // 'customer_id' => Customer::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}