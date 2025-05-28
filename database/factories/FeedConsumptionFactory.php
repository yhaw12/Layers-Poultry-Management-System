<?php

namespace Database\Factories;

use App\Models\FeedConsumption;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedConsumptionFactory extends Factory
{
    protected $model = FeedConsumption::class;

    public function definition()
    {
        return [
            // 'feed_id' will be set in the seeder
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'quantity' => $this->faker->numberBetween(10, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}