<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disease extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'symptoms',
        'treatments',
        'start_date',

    ];

    protected $dates = ['deleted_at'];
     public function birds()
    {
        return $this->hasMany(Bird::class);
    }

    public function healthChecks()
    {
        return $this->hasMany(HealthCheck::class, 'disease_id');
    }
    
}
