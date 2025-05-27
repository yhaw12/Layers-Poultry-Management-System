<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Employee;
use App\Models\MedicineLog;
use App\Models\Payroll;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Chicks;
use App\Models\Bird;
use App\Models\Mortalities;
use App\Models\Egg;
use App\Models\Feed;
use App\Models\Sale;
use App\Models\Customer;
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
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \Spatie\Backup\Events\BackupWasSuccessful::class => [
            \App\Listeners\LogBackupStatus::class,
        ],
        \Spatie\Backup\Events\BackupHasFailed::class => [
            \App\Listeners\LogBackupStatus::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Employee::observe(EmployeeObserver::class);
        MedicineLog::observe(MedicineLogObserver::class);
        Payroll::observe(PayrollObserver::class);
        Expense::observe(ExpenseObserver::class);
        Income::observe(IncomeObserver::class);
        Chicks::observe(ChicksObserver::class);
        Bird::observe(BirdObserver::class);
        Mortalities::observe(MortalitiesObserver::class);
        Egg::observe(EggObserver::class);
        Feed::observe(FeedObserver::class);
        Sale::observe(SaleObserver::class); // Fixed: Single observer for Sale
        Customer::observe(CustomerObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}