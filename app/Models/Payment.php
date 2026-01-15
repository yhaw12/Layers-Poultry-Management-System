<?php

namespace App\Models;

use App\Models\Sale;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'customer_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted()
    {
        // created: sync sale & create transaction (idempotent via reference_type/reference_id)
        static::created(function (Payment $payment) {
            try {
                $sale = Sale::withTrashed()->find($payment->sale_id);
                if ($sale) {
                    $asOf = Carbon::parse($payment->payment_date ?? now());
                    $sale->recalculatePaidAmount($asOf);
                    $sale->updatePaymentStatus($asOf);
                }

                Transaction::firstOrCreate(
                    [
                        'type' => 'payment',
                        'reference_type' => Payment::class,
                        'reference_id' => $payment->id,
                    ],
                    [
                        'amount' => $payment->amount,
                        'status' => 'approved',
                        'date' => $payment->payment_date ? $payment->payment_date->toDateString() : now()->toDateString(),
                        'source_type' => Sale::class,
                        'source_id' => $payment->sale_id,
                        'user_id' => $payment->created_by ?? null,
                        'description' => trim("Payment ID: {$payment->id} — " . ($payment->notes ?? '')),
                    ]
                );
            } catch (\Throwable $e) {
                Log::error('Error in Payment::created hook', [
                    'payment_id' => $payment->id ?? null,
                    'sale_id' => $payment->sale_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        // updated: update matching transaction if present (or create)
        static::updated(function (Payment $payment) {
            try {
                $sale = Sale::withTrashed()->find($payment->sale_id);
                if ($sale) {
                    $asOf = Carbon::parse($payment->payment_date ?? now());
                    $sale->recalculatePaidAmount($asOf);
                    $sale->updatePaymentStatus($asOf);
                }

                $tx = Transaction::where('type', 'payment')
                    ->where('reference_type', Payment::class)
                    ->where('reference_id', $payment->id)
                    ->first();

                $date = $payment->payment_date ? $payment->payment_date->toDateString() : now()->toDateString();
                $payload = [
                    'amount' => $payment->amount,
                    'date' => $date,
                    'source_type' => Sale::class,
                    'source_id' => $payment->sale_id,
                    'user_id' => $payment->created_by ?? null,
                    'description' => trim("Payment ID: {$payment->id} — " . ($payment->notes ?? '')),
                ];

                if ($tx) {
                    $tx->update($payload);
                } else {
                    $payload['type'] = 'payment';
                    $payload['status'] = 'approved';
                    $payload['reference_type'] = Payment::class;
                    $payload['reference_id'] = $payment->id;
                    Transaction::create($payload);
                }
            } catch (\Throwable $e) {
                Log::error('Error in Payment::updated hook', [
                    'payment_id' => $payment->id ?? null,
                    'sale_id' => $payment->sale_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        // deleted: recalc sale & remove transaction reference
        static::deleted(function (Payment $payment) {
            try {
                $sale = Sale::withTrashed()->find($payment->sale_id);
                if ($sale) {
                    $sale->recalculatePaidAmount();
                    $sale->updatePaymentStatus();
                }

                Transaction::where('type', 'payment')
                    ->where('reference_type', Payment::class)
                    ->where('reference_id', $payment->id)
                    ->delete();
            } catch (\Throwable $e) {
                Log::error('Error in Payment::deleted hook', [
                    'payment_id' => $payment->id ?? null,
                    'sale_id' => $payment->sale_id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
