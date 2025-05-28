<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        return [
            'category' => $this->faker->randomElement(['feed', 'medicine', 'labor', 'utilities']),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}