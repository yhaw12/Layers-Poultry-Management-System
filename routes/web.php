<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ChicksController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HenController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\EggController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MedicineLogController;

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);

Route::middleware('auth')->group(function () {
    // Dashboard and common resources for all authenticated users
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('expenses', ExpenseController::class);
    Route::resource('chicks',   ChicksController::class);
    Route::resource('hens',     HenController::class);
    Route::resource('feed',     FeedController::class);
    Route::resource('eggs',     EggController::class);
    Route::resource('income', IncomeController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('medicine-logs', MedicineLogController::class);

});
