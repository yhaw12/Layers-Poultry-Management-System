<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unit_price = $this->faker->randomFloat(2, 10, 200);
        return [
            'saleable_id' => null, 
            'saleable_type' => null, 
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_amount' => $quantity * $unit_price,
            'sale_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}