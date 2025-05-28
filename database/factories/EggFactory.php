<?php

namespace Database\Factories;

use App\Models\Egg;
use Illuminate\Database\Eloquent\Factories\Factory;

class EggFactory extends Factory
{
    protected $model = Egg::class;

    public function definition()
    {
        $sold = $this->faker->boolean(50);
        $crates = $this->faker->numberBetween(1, 50);
        return [
            'crates' => $this->faker->numberBetween(1, 50),'crates' => $crates,
            'date_laid' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'sold_quantity' => $sold ? $this->faker->numberBetween(1, $crates) : null,
            'sold_date' => $sold ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
            'sale_price' => $sold ? $this->faker->randomFloat(2, 10, 100) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}