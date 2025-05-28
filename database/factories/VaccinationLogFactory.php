<?php

namespace Database\Factories;

use App\Models\VaccinationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccinationLogFactory extends Factory
{
    protected $model = VaccinationLog::class;

    public function definition()
    {
        return [
            // 'bird_id' set in seeder
            'vaccine_name' => $this->faker->word(),
            'date_administered' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}