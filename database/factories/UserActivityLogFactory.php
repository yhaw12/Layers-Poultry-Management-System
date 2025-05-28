<?php

namespace Database\Factories;

use App\Models\UserActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActivityLogFactory extends Factory
{
    protected $model = UserActivityLog::class;

    public function definition()
    {
        return [
            // 'user_id' can be set in seeder if needed
            'action' => $this->faker->randomElement(['login', 'logout', 'register', 'update']),
            'details' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}