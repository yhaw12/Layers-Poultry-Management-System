<?php

namespace Database\Factories;

use App\Models\Pen;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenFactory extends Factory
{
    protected $model = Pen::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word . ' Flock', // Appending 'Flock' for clarity
        ];
    }
}
