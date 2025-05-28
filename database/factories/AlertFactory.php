<?php

namespace Database\Factories;

use App\Models\Alert;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['backup_success', 'backup_failed']),
            'message' => $this->faker->sentence(),
            'read_at' => $this->faker->boolean(50) ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}