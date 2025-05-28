<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition()
    {
        return [
            'source' => $this->faker->randomElement(['egg_sales', 'bird_sales', 'other']),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 500, 10000),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'synced_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}