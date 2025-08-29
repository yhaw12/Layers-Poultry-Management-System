<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivityLog extends Model
{
    use SoftDeletes;


    protected $fillable = ['user_id', 'action', 'details', 'created_at'];

     protected $casts = [
    'details' => 'string',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $dates = ['deleted_at'];
}

