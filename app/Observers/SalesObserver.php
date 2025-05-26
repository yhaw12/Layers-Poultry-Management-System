<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class SaleObserver
{
    public function created(Sale $sale)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Sale::class,
                'model_id'   => $sale->id,
                'description'=> "Created sale record: " . ($sale->description ?? 'ID ' . $sale->id),
            ]);
        }
    }

    public function updated(Sale $sale)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Sale::class,
                'model_id'   => $sale->id,
                'description'=> "Updated sale record: " . ($sale->description ?? 'ID ' . $sale->id),
            ]);
        }
    }

    public function deleted(Sale $sale)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Sale::class,
                'model_id'   => $sale->id,
                'description'=> "Deleted sale record: " . ($sale->description ?? 'ID ' . $sale->id),
            ]);
        }
    }

    public function restored(Sale $sale)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Sale::class,
                'model_id'   => $sale->id,
                'description'=> "Restored sale record: " . ($sale->description ?? 'ID ' . $sale->id),
            ]);
        }
    }

    public function forceDeleted(Sale $sale)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Sale::class,
                'model_id'   => $sale->id,
                'description'=> "Force deleted sale record: " . ($sale->description ?? 'ID ' . $sale->id),
            ]);
        }
    }
}
