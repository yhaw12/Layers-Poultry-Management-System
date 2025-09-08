<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccinationLog extends Model
{ 
    use SoftDeletes;

    protected $fillable = [
        'bird_id',
        'vaccine_name',
        'date_administered',
        'next_vaccination_date',
        'notes',
    ];

    public function bird()
    {
        return $this->belongsTo(Bird::class, 'bird_id');
    }

    protected $dates = ['deleted_at'];
}
