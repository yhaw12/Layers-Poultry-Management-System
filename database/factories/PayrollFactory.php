<?php

namespace Database\Factories;

use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition()
    {
        $base_salary = $this->faker->randomFloat(2, 500, 5000);
        $bonus = $this->faker->randomFloat(2, 0, 500);
        $deductions = $this->faker->randomFloat(2, 0, 200);
        return [
            // 'employee_id' set in seeder
            'pay_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'base_salary' => $base_salary,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'net_pay' => $base_salary + $bonus - $deductions,
            'status' => $this->faker->randomElement(['paid', 'pending']),
            'notes' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}