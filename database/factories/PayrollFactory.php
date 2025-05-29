<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition()
    {
        $employee = Employee::inRandomOrder()->first() ?? Employee::factory()->create();
        $bonus = $this->faker->randomFloat(2, 0, 500);
        $deductions = $this->faker->randomFloat(2, 0, 200);
        return [
            'employee_id' => $employee->id,
            'pay_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'base_salary' => $employee->monthly_salary,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'net_pay' => $employee->monthly_salary + $bonus - $deductions,
            'status' => $this->faker->randomElement(['paid', 'pending']),
            'notes' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}