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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeUnread($q) { return $q->where('is_read', false); }
    public function scopeForUserOrGlobal($q, $userId) {
        return $q->where(function ($s) use ($userId) {
            $s->where('user_id', $userId)->orWhereNull('user_id');
        });
    }
    
}



