<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{   
    use SoftDeletes;
    protected $fillable = [ 'status', 'total_amount'];
    protected $dates = ['deleted_at'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function sales() { return $this->hasMany(Sale::class); }
}