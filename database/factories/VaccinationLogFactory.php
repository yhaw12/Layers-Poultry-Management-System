<?php

namespace Database\Factories;

use App\Models\VaccinationLog;
use App\Models\Bird;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccinationLogFactory extends Factory
{
    protected $model = VaccinationLog::class;

    public function definition()
    {
        return [
            'vaccine_name' => $this->faker->word(),
            'bird_id' => Bird::inRandomOrder()->first()->id ?? Bird::factory(),
            'date_administered' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}