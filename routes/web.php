<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\AdminUserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BirdsController;
use App\Http\Controllers\ChicksController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicineLogController;
use App\Http\Controllers\MortalitiesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VaccinationLogController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login',  [UserController::class, 'showLoginForm'])->name('login');
    Route::post('login', [UserController::class, 'login']);

    Route::get('register',  [UserController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [UserController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Admin-Only Routes (under /admin, named admin.*)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
     ->group(function () {
         Route::resource('users', AdminUserController::class)->except('show');

         Route::get('activity-logs', [ActivityLogController::class, 'index'])
              ->name('activity-logs.index');

         Route::post('alerts/{alert}/read',   [ActivityLogController::class, 'read'])
              ->name('alerts.read');
         Route::post('alerts/dismiss-all',    [ActivityLogController::class, 'dismissAll'])
              ->name('alerts.dismiss-all');

         Route::resource('roles', RoleController::class)
              ->only(['index', 'store']);
     });

/*
|--------------------------------------------------------------------------
| Authenticated Routes (all logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // logout
    Route::post('logout', [UserController::class, 'logout'])->name('logout');

    // dashboard
    Route::get('/',                [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/export', [DashboardController::class, 'exportPDF'])
         ->name('dashboard.export')
         ->middleware('role:admin');

    /*
    |--------------------------------------------------------------
    | Re-expose alerts routes under their old names so your views
    | calling route('alerts.dismiss-all') still work.
    |--------------------------------------------------------------
    */
    Route::post('alerts/{alert}/read',   [ActivityLogController::class, 'read'])
         ->name('alerts.read')
         ->middleware('role:admin');

    Route::post('alerts/dismiss-all',    [ActivityLogController::class, 'dismissAll'])
         ->name('alerts.dismiss-all')
         ->middleware('role:admin');

    // core resources
    Route::resources([
        'birds'            => BirdsController::class,
        'chicks'           => ChicksController::class,
        'customers'        => CustomerController::class,
        'eggs'             => EggController::class,
        'employees'        => EmployeeController::class,
        'expenses'         => ExpenseController::class,
        'feed'             => FeedController::class,
        'income'           => IncomeController::class,
        'inventory'        => InventoryController::class,
        'medicine-logs'    => MedicineLogController::class,
        'mortalities'      => MortalitiesController::class,
        'orders'           => OrderController::class,
        'payroll'          => PayrollController::class,
        'sales'            => SalesController::class,
        'suppliers'        => SupplierController::class,
        'vaccination-logs' => VaccinationLogController::class,
    ]);

    // custom routes
    Route::delete('eggs/bulk', [EggController::class, 'bulkDelete'])
         ->name('eggs.bulkDelete');

    Route::get('feed/consumption', [FeedController::class, 'consumption'])
         ->name('feed.consumption');

    Route::get('medicine-logs/purchase',    [MedicineLogController::class, 'purchase'])
         ->name('medicine-logs.purchase');
    Route::get('medicine-logs/consumption', [MedicineLogController::class, 'consumption'])
         ->name('medicine-logs.consumption');

    Route::get('sales/eggs',  [SalesController::class, 'sales'])
         ->name('eggs.sales');
    Route::get('sales/birds', [SalesController::class, 'birdSales'])
         ->name('sales.birds');

    Route::post('sales/{sale}/status', [SalesController::class, 'updateStatus'])
         ->name('sales.updateStatus');
    Route::get('sales/{sale}/invoice', [SalesController::class, 'invoice'])
         ->name('sales.invoice');
    Route::get('sales/{sale}/email', [SalesController::class, 'emailInvoice'])
         ->name('sales.emailInvoice');
    Route::post('sales/{sale}/payment', [SalesController::class, 'recordPayment'])
    ->name('sales.recordPayment')
    ->middleware('auth');

    Route::get('invoices', [SalesController::class, 'invoices'])
         ->name('invoices.index');

    Route::post('payroll/generate', [PayrollController::class, 'generateMonthly'])
         ->name('payroll.generate');
    Route::get('payroll/export',    [PayrollController::class, 'exportPDF'])
         ->name('payroll.export')
         ->middleware('role:admin');

    // reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('export',   [ReportController::class, 'export'])->name('export');
        Route::match(['get','post'], '{type?}', [ReportController::class, 'index'])
             ->name('index');
        Route::get('custom',   [ReportController::class, 'custom'])->name('custom');
    });

    // health check & diseases
    Route::resource('health-checks', HealthCheckController::class)
         ->only(['index','create','store']);

    Route::get('diseases',               [DiseaseController::class, 'index'])
         ->name('diseases.index');
    Route::get('diseases/{disease}/history', [DiseaseController::class, 'history'])
         ->name('diseases.history');
    Route::post('diseases',              [DiseaseController::class, 'store'])
         ->name('diseases.store');

    // user role assignment
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])
         ->name('users.assign-role')
         ->middleware('role:admin');

    // custom alert rules
    Route::post('alerts/custom/create', [ActivityLogController::class, 'createCustom'])
         ->name('alerts.custom.create')
         ->middleware('role:admin');

    Route::post('/users/{user}/toggle-permission', [AdminUserController::class, 'togglePermission'])
         ->name('users.toggle-permission');
});