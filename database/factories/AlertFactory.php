<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['inventory', 'sale', 'mortality', 'backup_success', 'backup_failed']),
            'message' => $this->faker->sentence(),
            'user_id' => User::inRandomOrder()->first()->id ?? 1, // Fallback to user ID 1
            'read_at' => $this->faker->boolean(50) ? now() : null,
            'created_at' => $this->faker->dateTimeBetween('2025-04-01', '2025-06-30'),
            'updated_at' => now(),
        ];
    }
}