<?php

namespace Database\Factories;

use App\Models\Bird;
use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    protected $model = Feed::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['Corn', 'Wheat', 'Soybean', 'Pellet', 'Mash']),
            'supplier' => $this->faker->company(),
            'quantity' => $this->faker->numberBetween(50, 1000),
            'threshold' => $this->faker->numberBetween(50, 200), // Matches default 100 in migration
            'weight' => $this->faker->randomFloat(2, 50, 500),
            'purchase_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'cost' => $this->faker->randomFloat(2, 100, 1000),
            'synced_at' => $this->faker->optional(0.3)->dateTime(), // 30% chance of being set
            'bird_id' => Bird::inRandomOrder()->first()?->id, // Nullable foreign key
        ];
    }
}