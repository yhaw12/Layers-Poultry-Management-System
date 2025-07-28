<?php

namespace Database\Factories;

use App\Models\Egg;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EggFactory extends Factory
{
    protected $model = Egg::class;

    public function definition()
    {
        $sold = $this->faker->boolean(50);
        $crates = $this->faker->numberBetween(1, 50);
        return [
            'crates' => $this->faker->numberBetween(1, 100),
            'date_laid' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'sold_quantity' => $this->faker->optional()->numberBetween(0, 50),
            'sold_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'sale_price' => $this->faker->optional()->randomFloat(2, 5, 50),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}