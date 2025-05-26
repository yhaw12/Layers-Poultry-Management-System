<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class EmployeeObserver
{
    public function created(Employee $employee)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model_type' => Employee::class,
                'model_id' => $employee->id,
                'description' => "Created employee: {$employee->name}",
            ]);
        }
    }

    public function updated(Employee $employee)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'model_type' => Employee::class,
                'model_id' => $employee->id,
                'description' => "Updated employee: {$employee->name}",
            ]);
        }
    }

    public function deleted(Employee $employee)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'model_type' => Employee::class,
                'model_id' => $employee->id,
                'description' => "Deleted employee: {$employee->name}",
            ]);
        }
    }
}