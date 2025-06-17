<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VaccinationLog extends Model
{ 
    use SoftDeletes;
    protected $fillable = ['vaccine_name', 'date_administered', 'notes'];

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }
    protected $dates = ['deleted_at'];
}