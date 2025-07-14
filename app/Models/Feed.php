<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feed extends Model
{   
    use SoftDeletes;
    protected $table = 'feed';
    protected $fillable = ['type', 'supplier', 'quantity', 'weight', 'purchase_date', 'cost', 'synced_at'];
    protected $casts = ['purchase_date' => 'date', 'synced_at' => 'datetime'];
    protected $dates = ['deleted_at'];

    public function supplier() {
    return $this->belongsTo(Supplier::class);
    }
    public function bird()
    {
        return $this->belongsTo(Bird::class);
    }
}