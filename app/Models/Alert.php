<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Alert extends Model
{

    //  use SoftDeletes;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

   protected $fillable = ['id', 'user_id', 'type', 'message', 'is_read', 'url', 'read_at'];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

