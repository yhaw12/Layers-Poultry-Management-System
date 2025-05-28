<?php

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    protected $model = Feed::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['starter', 'grower', 'finisher', 'chick_mash', 'layers_mash']),
            'quantity' => $this->faker->numberBetween(50, 500),
            'weight' => $this->faker->randomFloat(2, 10, 100), 
            'purchase_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'cost' => $this->faker->randomFloat(2, 100, 1000),
            'synced_at' => $this->faker->boolean(50) ? now() : null,
            'supplier' => $this->faker->company(), 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}