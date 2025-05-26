<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Payroll;

class PayrollSeeder extends Seeder
{
    public function run()
    {
        $employee = Employee::create([
            'name' => 'John Doe',
            'monthly_salary' => 5000.00,
            'phone' => '1234567890', 
        ]);

        Payroll::create([
            'employee_id' => $employee->id,
            'pay_date' => now()->startOfMonth()->toDateString(),
            'base_salary' => 5000.00,
            'bonus' => 500.00,
            'deductions' => 200.00,
            'net_pay' => 5300.00,
            'status' => 'paid',
            'notes' => 'Sample payroll',
        ]);
    }
}