<?php

namespace Database\Factories;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        $types = ['vaccination_overdue', 'vaccination_upcoming', 'sawdust_due', 'payroll_overdue'];

        return [
            'type' => $this->faker->randomElement($types),
            'title' => ucfirst(str_replace('_', ' ', $this->faker->randomElement($types))),
            'message' => $this->faker->sentence(),
            'due_date' => Carbon::today()->addDays($this->faker->numberBetween(-5, 10)),
            'severity' => $this->faker->randomElement(['info', 'warning', 'critical']),
            'is_done' => $this->faker->boolean(20), // 20% chance it's done
            'meta' => null, // can be array/json if you use it
        ];
    }
}
