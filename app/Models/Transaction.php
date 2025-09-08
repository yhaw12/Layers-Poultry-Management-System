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
        'reference_type',
        'reference_id',
        'user_id',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Polymorphic relation to referenced record (e.g. Payment).
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
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
