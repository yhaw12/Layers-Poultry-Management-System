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
            // 'supplier_id' set in seeder as 'customer_id' seems incorrect here
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}