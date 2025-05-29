<?php

namespace Database\Factories;

use App\Models\Mortalities;
use App\Models\Bird;
use Illuminate\Database\Eloquent\Factories\Factory;

class MortalitiesFactory extends Factory
{
    protected $model = Mortalities::class;

    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'quantity' => $this->faker->numberBetween(1, 10),
            'cause' => $this->faker->randomElement(['disease', 'predator', 'accident']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}