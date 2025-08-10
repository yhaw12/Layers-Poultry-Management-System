<?php

namespace Database\Factories;

use App\Models\Egg;
use App\Models\User;
use App\Models\Pen;
use Illuminate\Database\Eloquent\Factories\Factory;

class EggFactory extends Factory
{
    protected $model = Egg::class;

    public function definition()
    {
        $crates = $this->faker->numberBetween(0, 10); // Whole number crates
        $additional_eggs = $this->faker->numberBetween(0, 29); // Partial crate eggs
        $total_eggs = ($crates * 30) + $additional_eggs;
        $is_cracked = $this->faker->boolean(20); // 20% chance of cracked
        $egg_size = $is_cracked ? null : $this->faker->randomElement(['small', 'medium', 'large']);

        return [
            'pen_id' => $this->faker->boolean(80) ? Pen::factory() : null, // 80% chance of having a pen
            'crates' => $crates,
            'total_eggs' => $total_eggs,
            'is_cracked' => $is_cracked,
            'egg_size' => $egg_size,
            'date_laid' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}