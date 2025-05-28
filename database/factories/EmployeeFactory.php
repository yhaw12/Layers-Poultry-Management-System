<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'monthly_salary' => $this->faker->randomFloat(2, 500, 5000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}