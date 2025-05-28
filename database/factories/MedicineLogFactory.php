<?php

namespace Database\Factories;

use App\Models\MedicineLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineLogFactory extends Factory
{
    protected $model = MedicineLog::class;

    public function definition()
    {
        return [
            'medicine_name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['purchase', 'consumption']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'unit' => $this->faker->randomElement(['ml', 'g', 'units']),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}