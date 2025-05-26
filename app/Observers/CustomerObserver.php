<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    public function created(Customer $customer)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'create',
                'model_type' => Customer::class,
                'model_id'   => $customer->id,
                'description'=> "Created customer: " . ($customer->name ?? $customer->email ?? 'ID ' . $customer->id),
            ]);
        }
    }

    public function updated(Customer $customer)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'update',
                'model_type' => Customer::class,
                'model_id'   => $customer->id,
                'description'=> "Updated customer: " . ($customer->name ?? $customer->email ?? 'ID ' . $customer->id),
            ]);
        }
    }

    public function deleted(Customer $customer)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'delete',
                'model_type' => Customer::class,
                'model_id'   => $customer->id,
                'description'=> "Deleted customer: " . ($customer->name ?? $customer->email ?? 'ID ' . $customer->id),
            ]);
        }
    }

    public function restored(Customer $customer)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'restore',
                'model_type' => Customer::class,
                'model_id'   => $customer->id,
                'description'=> "Restored customer: " . ($customer->name ?? $customer->email ?? 'ID ' . $customer->id),
            ]);
        }
    }

    public function forceDeleted(Customer $customer)
    {
        if (Auth::check()) {
            UserActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'force_delete',
                'model_type' => Customer::class,
                'model_id'   => $customer->id,
                'description'=> "Force deleted customer: " . ($customer->name ?? $customer->email ?? 'ID ' . $customer->id),
            ]);
        }
    }
}
