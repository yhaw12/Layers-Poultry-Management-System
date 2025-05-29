<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VaccinationLog extends Model
{
    protected $fillable = ['vaccine_name', 'date_administered', 'notes'];

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }
}