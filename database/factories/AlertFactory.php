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
        $type = $this->faker->randomElement(['inventory', 'sale', 'mortality', 'backup_success', 'backup_failed']);
        $isRead = $this->faker->boolean(50);

        return [
            'type' => $type,
            'message' => $this->faker->sentence(),
            'user_id' => User::inRandomOrder()->first()->id ?? 1,
            'is_read' => $isRead,
            'url' => $this->faker->optional(0.7)->url(), // 70% chance of having a URL
            'read_at' => $isRead ? $this->faker->dateTimeBetween('2025-04-01', '2025-06-30') : null,
            'created_at' => $this->faker->dateTimeBetween('2025-04-01', '2025-06-30'),
            'updated_at' => now(),
        ];
    }
}