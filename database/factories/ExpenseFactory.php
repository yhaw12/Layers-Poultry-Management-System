<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        return [
            'category' => $this->faker->randomElement(['Feed', 'Equipment', 'Labor', 'Utilities', 'Maintenance']),
            'description' => $this->faker->optional()->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'synced_at' => $this->faker->optional()->dateTimeThisYear(),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}