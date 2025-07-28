<?php

namespace Database\Factories;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    protected $model = Income::class;

    public function definition()
    {
        return [
            'source' => $this->faker->randomElement(['egg_sales', 'bird_sales', 'other']),
            'description' => $this->faker->optional()->sentence(),
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'synced_at' => $this->faker->optional()->dateTimeThisYear(),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}