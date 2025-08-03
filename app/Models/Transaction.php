<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'amount',
        'status',
        'date',
        'source_type',
        'source_id',
        'user_id',
        'description',
    ];

    public function source()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function updateStatus(string $status)
    {
        $this->update(['status' => $status]);
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated_transaction_status',
            'details' => "Updated transaction #{$this->id} status to {$status}",
        ]);
    }
}