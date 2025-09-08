<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mortalities extends Model
{
    use SoftDeletes;

    protected $fillable = ['bird_id', 'date', 'quantity', 'cause'];

    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }

    protected $dates = ['deleted_at'];
}