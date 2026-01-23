<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'pay_date',
         'base_salary',
        'bonus',
        'deductions',
        'net_pay',
        'status',
        'notes',
        'expense_id'
    ];
    protected $dates = ['deleted_at'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}