<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MaintenanceLog extends Model
{
use HasFactory;


protected $fillable = [
'user_id', 'task', 'performed_at', 'notes',
];


protected $casts = [
'performed_at' => 'datetime',
];


public function user()
{
return $this->belongsTo(User::class);
}
}