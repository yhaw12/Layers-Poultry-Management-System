<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Employee;
use App\Models\MedicineLog;
use App\Models\Payroll;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Chicks;
use App\Models\Birds;
use App\Models\Mortalities;
use App\Models\Eggs;
use App\Models\Feed;
use App\Models\Sales;
use App\Models\Customer;
use App\Models\Sale;
use App\Observers\EmployeeObserver;
use App\Observers\MedicineLogObserver;
use App\Observers\PayrollObserver;
use App\Observers\ExpenseObserver;
use App\Observers\IncomeObserver;
use App\Observers\ChicksObserver;
use App\Observers\BirdObserver;
use App\Observers\MortalitiesObserver;
use App\Observers\EggObserver;
use App\Observers\FeedObserver;
use App\Observers\SaleObserver;
use App\Observers\CustomerObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Other events...
    ];

    public function boot()
    {
        Employee::observe(EmployeeObserver::class);
        MedicineLog::observe(MedicineLogObserver::class);
        Payroll::observe(PayrollObserver::class);
        Expense::observe(ExpenseObserver::class);
        Income::observe(IncomeObserver::class);
        Chicks::observe(ChicksObserver::class);
        Sale::observe(BirdObserver::class);
        Mortalities::observe(MortalitiesObserver::class);
        Sale::observe(EggObserver::class);
        Feed::observe(FeedObserver::class);
        Sale::observe(SaleObserver::class);
        Customer::observe(CustomerObserver::class);
    }
}