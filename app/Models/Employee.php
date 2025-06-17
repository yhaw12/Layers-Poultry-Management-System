<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'monthly_salary',
    ];
     public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
    protected $dates = ['deleted_at'];
    
}
