<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'monthly_salary',
    ];
     public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
