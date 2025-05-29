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
            'type' => $this->faker->word(),
            'supplier' => $this->faker->company(), // String instead of supplier_id
            'quantity' => $this->faker->numberBetween(100, 1000),
            'weight' => $this->faker->randomFloat(2, 50, 500),
            'purchase_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'cost' => $this->faker->randomFloat(2, 100, 1000),
            'synced_at' => $this->faker->optional()->dateTime(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}