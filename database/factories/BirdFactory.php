<?php

namespace Database\Factories;

use App\Models\Bird;
use Illuminate\Database\Eloquent\Factories\Factory;

class BirdFactory extends Factory
{
    protected $model = Bird::class;

    public function definition()
    {
        return [
            'breed' => $this->faker->word(),
            'type' => $this->faker->randomElement(['layer', 'broiler']),
            'quantity' => $this->faker->numberBetween(10, 100),
            'working' => $this->faker->boolean(),
            'age' => $this->faker->numberBetween(1, 52), // weeks
            'entry_date' => $this->faker->date(),
            'synced_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}