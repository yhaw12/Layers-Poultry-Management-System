<?php

namespace Database\Factories;

use App\Models\Bird;
use Illuminate\Database\Eloquent\Factories\Factory;

class BirdFactory extends Factory
{
    protected $model = Bird::class;

    public function definition()
    {
        $quantityBought = $this->faker->numberBetween(10, 100);
        $alive = $this->faker->numberBetween(0, $quantityBought);
        $dead = $this->faker->numberBetween(0, $quantityBought - $alive);
        $purchaseDate = $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');
        $entryDate = $this->faker->dateTimeBetween($purchaseDate, 'now')->format('Y-m-d');

        return [
            'breed' => $this->faker->randomElement(['Rhode Island Red', 'Leghorn', 'Plymouth Rock', 'Cobb 500', 'Ross 308']),
            'type' => $this->faker->randomElement(['layer', 'broiler']),
            'quantity' => $alive,
            'quantity_bought' => $quantityBought,
            'feed_amount' => $this->faker->randomFloat(2, 0, 100),
            'alive' => $alive,
            'dead' => $dead,
            'purchase_date' => $purchaseDate,
            'cost' => $this->faker->randomFloat(2, 10, 1000),
            'working' => $this->faker->boolean(80),
            'age' => $this->faker->numberBetween(0, 52),
            'entry_date' => $entryDate,
            'vaccination_status' => $this->faker->optional(0.7)->boolean(),
            // 'housing_location' => $this->faker->optional(0.8)->randomElement(['Barn A', 'Coop B', 'Shed C', 'Pen D']),
            'stage' => $this->faker->randomElement(['chick', 'juvenile', 'adult']),
            'synced_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}