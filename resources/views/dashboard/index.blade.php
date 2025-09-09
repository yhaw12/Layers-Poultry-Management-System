@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl overflow-hidden">
    <!-- Header Section -->
    <header class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Dashboard</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Your farm management overview</p>
    </header>

    <!-- Date Filter -->
    <section class="mb-8">
        <div class="container-box bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Date Range Filter</h2>
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" aria-label="Select start date">
                </div>

                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" aria-label="Select end date">
                </div>

                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200" aria-label="Apply date filter">Filter</button>
            </form>
        </div>
    </section>

    <!-- Quick Actions, Weather Widget, and Task Calendar -->
    <section class="mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions -->
            <div class="container-box bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @role('admin')
                        @can('create_birds')
                            <a href="{{ route('birds.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new bird">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Bird
                            </a>
                        @endcan
                        @can('create_eggs')
                            <a href="{{ route('eggs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105" aria-label="Record egg production">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Record Egg Production
                            </a>
                        @endcan
                        @can('create_sales')
                            <a href="{{ route('sales.create') }}" class="flex items-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new sale">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Add Sale
                            </a>
                        @endcan
                        @can('create_expenses')
                            <a href="{{ route('expenses.create') }}" class="flex items-center bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new expense">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Log Expense
                            </a>
                        @endcan
                        @can('create_income')
                            <a href="{{ route('income.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new income">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Log Income
                            </a>
                        @endcan
                        @can('create_users')
                            <a href="{{ route('users.create') }}" class="flex items-center bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new user">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2m6-4v4m-4 0h8"/>
                                </svg>
                                Add User
                            </a>
                        @endcan
                        @can('create_employees')
                            <a href="{{ route('employees.create') }}" class="flex items-center bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new employee">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Add Employee
                            </a>
                        @endcan
                    @endrole

                    @role('farm_manager')
                        @can('create_birds')
                            <a href="{{ route('birds.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new bird">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Bird
                            </a>
                        @endcan
                        @can('create_eggs')
                            <a href="{{ route('eggs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105" aria-label="Record egg production">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Record Egg Production
                            </a>
                        @endcan
                        @can('create_mortalities')
                            <a href="{{ route('mortalities.create') }}" class="flex items-center bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new mortality">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Log Mortality
                            </a>
                        @endcan
                        @can('create_feed')
                            <a href="{{ route('feed.create') }}" class="flex items-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new feed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v4H3V3zm0 6h18v4H3V9zm0 6h18v4H3v-4z"/>
                                </svg>
                                Log Feed
                            </a>
                        @endcan
                        @can('create_inventory')
                            <a href="{{ route('inventory.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new inventory">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                Add Inventory
                            </a>
                        @endcan
                    @endrole

                    @role('accountant')
                        @can('create_expenses')
                            <a href="{{ route('expenses.create') }}" class="flex items-center bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new expense">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Log Expense
                            </a>
                        @endcan
                        @can('create_income')
                            <a href="{{ route('income.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new income">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Log Income
                            </a>
                        @endcan
                        @can('create_payroll')
                            <a href="{{ route('payroll.create') }}" class="flex items-center bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new payroll">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Log Payroll
                            </a>
                        @endcan
                    @endrole

                    @role('sales_manager')
                        @can('create_sales')
                            <a href="{{ route('sales.create') }}" class="flex items-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new sale">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Add Sale
                            </a>
                        @endcan
                        @can('create_customers')
                            <a href="{{ route('customers.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new customer">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Add Customer
                            </a>
                        @endcan
                        @can('create_orders')
                            <a href="{{ route('orders.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105" aria-label="Create new order">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Create Order
                            </a>
                        @endcan
                        @can('generate_invoices')
                            <a href="{{ route('invoices.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105" aria-label="Generate new invoice">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/>
                                </svg>
                                Generate Invoice
                            </a>
                        @endcan
                    @endrole

                    @role('inventory_manager')
                        @can('create_inventory')
                            <a href="{{ route('inventory.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new inventory">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                Add Inventory
                            </a>
                        @endcan
                        @can('create_suppliers')
                            <a href="{{ route('suppliers.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105" aria-label="Add new supplier">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Add Supplier
                            </a>
                        @endcan
                        @can('create_feed')
                            <a href="{{ route('feed.create') }}" class="flex items-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new feed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v4H3V3zm0 6h18v4H3V9zm0 6h18v4H3v-4z"/>
                                </svg>
                                Log Feed
                            </a>
                        @endcan
                        @can('create_medicine_logs')
                            <a href="{{ route('medicine-logs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new medicine">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2m-6 0h6"/>
                                </svg>
                                Log Medicine
                            </a>
                        @endcan
                    @endrole

                    @role('veterinarian')
                        @can('create_health_checks')
                            <a href="{{ route('health-checks.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new health check">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Log Health Check
                            </a>
                        @endcan
                        @can('create_diseases')
                            <a href="{{ route('diseases.create') }}" class="flex items-center bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new disease">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Log Disease
                            </a>
                        @endcan
                        @can('create_vaccination_logs')
                            <a href="{{ route('vaccination-logs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new vaccination">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Log Vaccination
                            </a>
                        @endcan
                        @can('create_medicine_logs')
                            <a href="{{ route('medicine-logs.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new medicine">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2m-6 0h6"/>
                                </svg>
                                Log Medicine
                            </a>
                        @endcan
                    @endrole

                    @role('labourer')
                        @can('create_eggs')
                            <a href="{{ route('eggs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105" aria-label="Record egg production">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Record Egg Production
                            </a>
                        @endcan
                        @can('create_mortalities')
                            <a href="{{ route('mortalities.create') }}" class="flex items-center bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200 transform hover:scale-105" aria-label="Log new mortality">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Log Mortality
                            </a>
                        @endcan
                    @endrole
                </div>
            </div>

            <!-- Weather Widget and Task Calendar -->
            <div class="flex flex-col gap-6">
                <!-- Weather Widget -->
                <div class="container-box bg-gradient-to-r from-blue-100 to-gray-50 dark:from-blue-900 dark:to-gray-800 shadow-lg rounded-xl p-4 max-w-[300px] transition-all duration-500 hover:shadow-2xl hover:-translate-y-1 hover:scale-[1.02]">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                            Weather
                        </h2>
                        <div id="weather-icon" aria-hidden="true"></div>
                    </div>

                    <div id="weather-widget" class="space-y-1">
                        <p id="weather-temp" class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 transition-all duration-500">
                            {{ isset($weather) && $weather['ok'] ? $weather['temperature'] . '°C' : '--°C' }}
                        </p>
                        <p id="weather-condition" class="text-base text-gray-700 dark:text-gray-300 capitalize">
                            {{ isset($weather) && $weather['ok'] ? $weather['condition'] : 'Unavailable' }}
                        </p>
                        <p id="weather-location" class="text-xs text-gray-500 dark:text-gray-400">
                            {{ isset($weather) && $weather['ok'] ? $weather['location'] : '---' }}
                        </p>
                    </div>
                </div>

                <!-- Task Calendar -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-[400px] mx-auto perspective-1000">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Task Calendar
                        </h2>
                        <span id="reminder-count" class="text-sm text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded-full">{{ isset($reminders) ? $reminders->count() : 0 }} pending</span>
                    </div>

                    <div id="reminder-rotator" class="relative h-40 overflow-hidden">
                        @if(isset($reminders) && $reminders->isNotEmpty())
                            @foreach($reminders as $i => $reminder)
                                <div class="absolute inset-0 transition-transform duration-700 ease-in-out transform origin-top {{ $i === 0 ? 'rotate-x-0 opacity-100' : 'rotate-x-90 opacity-0' }}" data-reminder="{{ $i }}" data-id="{{ $reminder->id }}">
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-inner">
                                        <div class="text-center mb-3 bg-white dark:bg-gray-800 rounded-md shadow p-2">
                                            <span class="text-3xl font-bold text-gray-800 dark:text-white block">{{ \Carbon\Carbon::parse($reminder->due_date)->format('d') }}</span>
                                            <span class="text-sm uppercase text-gray-600 dark:text-gray-400 block">{{ \Carbon\Carbon::parse($reminder->due_date)->format('M Y') }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-500 block">{{ \Carbon\Carbon::parse($reminder->due_date)->format('D') }}</span>
                                        </div>

                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1 truncate">{{ $reminder->title }}</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $reminder->message }}</p>

                                        <div class="flex justify-between items-center">
                                            <span class="px-2 py-1 text-xs font-bold rounded-full
                                                @if($reminder->severity === 'critical') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                                @elseif($reminder->severity === 'warning') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                                                @else bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 @endif">
                                                {{ strtoupper($reminder->severity) }}
                                            </span>
                                            <button class="mark-read-btn text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center" data-id="{{ $reminder->id }}" aria-label="Mark reminder as done">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Done
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No reminders available.</p>
                        @endif
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <button id="prev-reminder" class="p-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600" aria-label="Previous reminder">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <div class="flex space-x-1">
                            @if(isset($reminders) && $reminders->isNotEmpty())
                                @foreach($reminders as $i => $reminder)
                                    <button class="w-2 h-2 rounded-full reminder-dot {{ $i === 0 ? 'bg-blue-600' : 'bg-gray-400' }}" data-index="{{ $i }}" aria-label="Go to reminder {{ $i + 1 }}"></button>
                                @endforeach
                            @endif
                        </div>

                        <button id="next-reminder" class="p-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600" aria-label="Next reminder">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Financial and Transaction Section -->
    <section class="mb-8">
        @can('manage_finances')
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Financial Summary</h2>
                @if (isset($totalExpenses, $totalIncome, $profit))
                    @php
                        $cards = [
                            ['label' => 'Expenses', 'value' => $totalExpenses ?? 0, 'icon' => '💸', 'color' => 'red', 'trend' => $expenseTrend ?? []],
                            ['label' => 'Income', 'value' => $totalIncome ?? 0, 'icon' => '💰', 'color' => 'green', 'trend' => $incomeTrend ?? []],
                            ['label' => 'Profit', 'value' => $profit ?? 0, 'icon' => '📈', 'color' => ($profit ?? 0) >= 0 ? 'green' : 'red', 'trend' => $profitTrend ?? []],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($cards as $card)
                            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-base sm:text-lg">{{ $card['label'] }}</h3>
                                    <span class="text-xl sm:text-2xl">{{ $card['icon'] }}</span>
                                </div>

                                <p class="text-xl sm:text-2xl font-bold mt-4 truncate">
                                    ₵{{ number_format($card['value'], 2) }}
                                </p>

                                <div class="relative h-12 mt-4">
                                    <canvas id="{{ strtolower($card['label']) }}Trend" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded-2xl" role="alert">
                        Financial data is currently unavailable.
                    </div>
                @endif
            </div>
        @else
            <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded-2xl mb-6" role="alert">
                You do not have permission to view the financial summary.
            </div>
        @endcan

        <!-- Key Performance Indicators (KPIs) -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Performance Indicators (KPIs)</h2>

            @php
                $groupedKpis = [
                    'Flock Statistics' => [
                        ['label' => 'Chicks', 'value' => $chicks ?? 0, 'icon' => '🐤'],
                        ['label' => 'Layers', 'value' => $layerBirds ?? 0, 'icon' => '🐓'],
                        ['label' => 'Broilers', 'value' => $broilerBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Total Birds', 'value' => $totalBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Mortality %', 'value' => number_format($mortalityRate ?? 0, 2), 'icon' => '⚰️'],
                    ],
                    'Production' => [
                        ['label' => 'Egg Crates', 'value' => $metrics['egg_crates'] ?? 0, 'icon' => '🥚'],
                        ['label' => 'Feed (kg)', 'value' => $metrics['feed_kg'] ?? 0, 'icon' => '🌾'],
                        ['label' => 'FCR', 'value' => number_format($fcr ?? 0, 2), 'icon' => '⚖️'],
                    ],
                    'Operations' => [
                        ['label' => 'Employees', 'value' => $employees ?? 0, 'icon' => '👨‍🌾'],
                        ['label' => 'Payroll', 'value' => number_format($totalPayroll ?? 0, 2), 'icon' => '💵'],
                        ['label' => 'Sales', 'value' => $metrics['sales'] ?? 0, 'icon' => '🛒'],
                        ['label' => 'Customers', 'value' => $metrics['customers'] ?? 0, 'icon' => '👥'],
                        ['label' => 'Med Bought', 'value' => $metrics['medicine_buy'] ?? 0, 'icon' => '💊'],
                        ['label' => 'Med Used', 'value' => $metrics['medicine_use'] ?? 0, 'icon' => '🩺'],
                    ],
                ];
            @endphp

            @foreach ($groupedKpis as $group => $kpis)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $group }}</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($kpis as $item)
                            @if ($group === 'Operations' && in_array($item['label'], ['Employees', 'Payroll', 'Sales', 'Customers']))
                                @role('admin')
                                    <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-4 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                            <span class="text-xl sm:text-2xl">{{ $item['icon'] }}</span>
                                        </div>

                                        <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $item['value'] }}</p>
                                    </div>
                                @endrole
                            @else
                                <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-4 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                        <span class="text-xl sm:text-2xl">{{ $item['icon'] }}</span>
                                    </div>

                                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $item['value'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        @can('manage_finances')
            <div class="mb-6">
                <div class="container-box bg-gradient-to-r from-yellow-100 to-gray-50 dark:from-yellow-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow duration-200 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pending Approvals
                        </h2>

                        <svg class="w-8 h-8 text-yellow-500 dark:text-yellow-400 approval-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <div id="pending-approvals">
                        @if (isset($pendingApprovals) && $pendingApprovals->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach ($pendingApprovals->take(3) as $approval)
                                    <li class="transition-all duration-300 hover:scale-105">
                                        <div class="flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900">
                                            <div>
                                                <p class="text-base text-gray-600 dark:text-gray-400">{{ $approval->date }}</p>
                                                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                                    ₵{{ number_format($approval->amount, 2) }}
                                                    <span class="text-sm ml-2 px-2 py-1 rounded-full {{ $approval->type === 'expense' ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' }}">
                                                        {{ ucfirst($approval->type) }}
                                                    </span>
                                                </p>
                                                <p class="text-base text-gray-600 dark:text-gray-300">{{ Str::limit($approval->description, 40) }}</p>
                                            </div>

                                            <div class="flex space-x-3">
                                                <a href="{{ route('transactions.approve', $approval->id) }}" class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="Approve transaction">Approve</a>
                                                <a href="{{ route('transactions.reject', $approval->id) }}" class="bg-red-600 text-white py-1 px-3 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="Reject transaction">Reject</a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            @if ($pendingApprovals->count() > 3)
                                <div class="mt-4 text-right">
                                    <a href="{{ route('transactions.index') }}" class="text-base text-blue-600 dark:text-blue-400 hover:underline transition duration-200" aria-label="View all pending approvals">View All →</a>
                                </div>
                            @endif
                        @else
                            <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No pending approvals.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endcan

        <div class="container-box bg-gradient-to-r from-blue-100 to-gray-50 dark:from-blue-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow duration-200 hover:shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Payroll Status
                </h2>

                <svg class="w-8 h-8 text-blue-500 dark:text-blue-400 payroll-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <div id="payroll-status">
                @if (isset($payrollStatus) && $payrollStatus->isNotEmpty())
                    @php $latest = $payrollStatus->first(); @endphp
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900 hover:scale-105">
                            <p class="text-base text-gray-600 dark:text-gray-400">Latest Pay Date</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $latest->date }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900 hover:scale-105">
                            <p class="text-base text-gray-600 dark:text-gray-400">Employees</p>
                            <p class="text-2xl font-bold">{{ $latest->employees }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-blue-900 hover:scale-105">
                            <p class="text-base text-gray-600 dark:text-gray-400">Total Paid</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">₵{{ number_format($latest->total, 2) }}</p>
                        </div>

                        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg transition-all duration-300 hover:scale-105">
                            <p class="text-base text-gray-600 dark:text-gray-400 mb-1">Status</p>
                            <span class="px-2 py-1 text-sm font-medium rounded-full {{ $latest->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                                {{ ucfirst($latest->status) }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No payroll activity yet.</p>
                @endif

                <div class="mt-4 text-right">
                    <a href="{{ route('payroll.index') }}" class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all payroll records">View All</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Transaction and Order Overview -->
    <section class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="container-box bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-all duration-200 hover:shadow-xl hover:scale-105 border-2 border-transparent hover:border-indigo-500">
                <a href="{{ route('transactions.index') }}" class="block" aria-label="View all pending transactions">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Transactions</h4>
                        </div>

                        <button class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all pending transactions">View All</button>
                    </div>

                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $pendingTransactions ?? 0 }}</p>
                    <div class="h-12 mt-4">
                        <canvas id="pendingTransactionsTrend" class="w-full h-full"></canvas>
                    </div>
                </a>
            </div>

            <div class="container-box bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-all duration-200 hover:shadow-xl hover:scale-105 border-2 border-transparent hover:border-indigo-500">
                <a href="{{ route('transactions.index') }}" class="block" aria-label="View total transaction amount">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Transaction Amount</h4>
                        </div>

                        <button class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all transactions">View All</button>
                    </div>

                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">₵{{ number_format($totalTransactionAmount ?? 0, 2) }}</p>
                    <div class="h-12 mt-4">
                        <canvas id="totalTransactionAmountTrend" class="w-full h-full"></canvas>
                    </div>
                </a>
            </div>

            <div class="container-box bg-gradient-to-r from-purple-50 to-white dark:from-purple-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-all duration-200 hover:shadow-xl hover:scale-105 border-2 border-transparent hover:border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Pending Orders</h4>
                    </div>

                    <a href="{{ route('orders.create') }}" class="bg-purple-600 text-white py-1 px-3 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="Create a new order">Create Order</a>
                </div>

                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $pendingOrders ?? 0 }}</p>

                <div class="mt-4">
                    <div class="flex mb-2 items-center justify-between">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Completion</span>
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $completionPercentage ?? 0 }}%</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700" aria-hidden="true">
                        <div id="completionProgressBar" class="bg-purple-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $completionPercentage ?? 0 }}%"></div>
                    </div>

                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <span id="completionPercentageValue">{{ $completionPercentage ?? 0 }}%</span>
                        <span class="text-[10px]">Completion</span>
                    </div>
                </div>
            </div>

            <div class="container-box bg-gradient-to-r from-purple-50 to-white dark:from-purple-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-all duration-200 hover:shadow-xl hover:scale-105 border-2 border-transparent hover:border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Total Order Amount</h4>
                    </div>

                    <a href="{{ route('orders.index') }}" class="bg-purple-600 text-white py-1 px-3 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium transition duration-200 transform hover:scale-105" aria-label="View all orders">View All</a>
                </div>

                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">₵{{ number_format($totalOrderAmount ?? 0, 2) }}</p>
                <div class="h-12 mt-4">
                    <canvas id="totalOrderAmountTrend" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </section>

    <!-- Sales and Mortality -->
    <section class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow duration-200 hover:shadow-xl hover:scale-105">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"></path>
                    </svg>
                    Recent Sales
                </h2>

                <div class="flex mb-4 space-x-2">
                    <button class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-green-500 focus:border-green-500 transition duration-200" data-tab="egg" aria-label="Show egg sales">Egg Sales</button>
                    <button class="tab-btn px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:border-green-500 focus:border-green-500 transition duration-200" data-tab="bird" aria-label="Show bird sales">Bird Sales</button>
                </div>

                <div class="tab-content" id="egg-sales">
                    <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg transition-all duration-200 hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Egg Sales</h4>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">₵{{ number_format($eggSales ?? 0, 2) }}</p>
                    </div>
                </div>

                <div class="tab-content hidden" id="bird-sales">
                    <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg transition-all duration-200 hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Bird Sales</h4>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">₵{{ number_format($birdSales ?? 0, 2) }}</p>
                    </div>
                </div>

                <div class="chart-container mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title text-lg font-medium text-gray-700 dark:text-gray-300">Sales Comparison</h4>
                        <select id="salesComparisonChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select sales comparison chart type">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>

                    <div class="relative h-64">
                        <canvas id="salesComparison" class="w-full h-full"></canvas>
                    </div>

                    @if (!isset($salesComparison) || $salesComparison->isEmpty())
                        <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No sales comparison data available.</p>
                    @endif
                </div>
            </div>

            <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow duration-200 hover:shadow-xl hover:scale-105">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Recent Mortalities
                </h2>

                <div class="flex justify-between mb-4">
                    <div class="relative">
                        <select class="border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" id="mortalityFilter" aria-label="Filter mortalities by cause">
                            <option value="all">All Causes</option>
                            <option value="disease">Disease</option>
                            <option value="injury">Injury</option>
                        </select>
                    </div>

                    <a href="{{ route('mortalities.create') }}" class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 focus:ring-2 focus:ring-red-500 transition duration-200 transform hover:scale-105" aria-label="Log a new mortality">Log Mortality</a>
                </div>

                <div class="chart-container">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title text-lg font-medium text-gray-700 dark:text-gray-300">Mortality Trend</h4>
                        <select id="mortalityTrendChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select mortality trend chart type">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>

                    <div class="relative h-64">
                        <canvas id="mortalityTrend" class="w-full h-full"></canvas>
                    </div>

                    @if (!isset($mortalityTrend) || $mortalityTrend->isEmpty())
                        <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No mortality data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Role-Specific Sections -->
    <section class="mb-8">
        @role('labourer')
            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daily Instructions</h2>
                @if (isset($dailyInstructions) && $dailyInstructions->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($dailyInstructions as $item)
                            <li class="list-item transition-all duration-200 hover:scale-105">
                                <span class="highlight">{{ $item->content }}</span> (Posted: {{ $item->created_at->format('Y-m-d H:i') }})
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No instructions for today.</p>
                @endif
            </div>
        @endrole

        @role('farm_manager')
            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flock Health Summary</h2>
                @if (isset($healthSummary) && $healthSummary->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($healthSummary as $item)
                            <li class="list-item transition-all duration-200 hover:scale-105">
                                <span class="highlight">{{ $item->date->format('Y-m-d') }}</span>: {{ $item->checks }} checks, {{ $item->unhealthy }} unhealthy
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No recent health checks.</p>
                @endif
            </div>
        @endrole

        @role('veterinarian')
            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Vaccination Schedule</h2>
                @if (isset($vaccinationSchedule) && $vaccinationSchedule->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($vaccinationSchedule as $item)
                            <li class="list-item transition-all duration-200 hover:scale-105">
                                <div class="flex justify-between items-center">
                                    <span>
                                        <span class="highlight">{{ $item->vaccine_name }}</span> (Due: {{ $item->due_date->format('Y-m-d') }})
                                    </span>
                                    <form action="{{ route('vaccinations.complete', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm" aria-label="Mark vaccination as complete">Mark as Complete</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No upcoming vaccinations.</p>
                @endif
            </div>
        @endrole

        @role('inventory_manager')
            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Suppliers</h2>
                @if (isset($suppliers) && $suppliers->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach ($suppliers as $item)
                            <li class="list-item transition-all duration-200 hover:scale-105">
                                <div class="flex justify-between items-center">
                                    <span>
                                        <span class="highlight">{{ $item->name }}</span> ({{ $item->contact_info }})
                                    </span>
                                    <div>
                                        <a href="{{ route('inventory.create', ['supplier_id' => $item->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mr-4" aria-label="Add inventory for {{ $item->name }}">Add Inventory</a>
                                        <a href="{{ route('suppliers.show', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm" aria-label="View details for {{ $item->name }}">View Details</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No suppliers found.</p>
                @endif
            </div>
        @endrole
    </section>

    <!-- Vaccination Overview -->
    <section class="mb-8">
        <div class="container-box bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-lg rounded-xl p-6 transition-shadow duration-200 hover:shadow-xl hover:scale-105">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Vaccination Overview
            </h2>

            <div class="mb-4">
                <h4 class="text-lg font-medium text-gray-700 dark:text-gray-300">Upcoming Vaccinations</h4>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">₵{{ number_format($upcomingVaccinations ?? 0, 2) }}</p>
            </div>
        </div>
    </section>

    <!-- Dashboard Charts -->
    <section class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Performance Trends</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="chart-container bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="chart-title">Egg Trend</h4>
                    <select id="eggChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select egg trend chart type">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>

                <div class="relative h-64">
                    <canvas id="eggTrend" class="w-full h-full"></canvas>
                </div>

                @if (!isset($eggTrend) || $eggTrend->isEmpty())
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No egg data available.</p>
                @endif
            </div>

            <div class="chart-container bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="chart-title">Feed Trend</h4>
                    <select id="feedChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select feed trend chart type">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>

                <div class="relative h-64">
                    <canvas id="feedTrend" class="w-full h-full"></canvas>
                </div>

                @if (!isset($feedTrend) || $feedTrend->isEmpty())
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No feed data available.</p>
                @endif
            </div>

            @role('admin')
                <div class="chart-container bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title">Sales Trend</h4>
                        <select id="salesChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select sales trend chart type">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>

                    <div class="relative h-64">
                        <canvas id="salesTrend" class="w-full h-full"></canvas>
                    </div>

                    @if (!isset($salesTrend) || $salesTrend->isEmpty())
                        <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No sales data available.</p>
                    @endif
                </div>
            @endrole

            <div class="chart-container bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-200 hover:shadow-xl hover:scale-105">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="chart-title">Income Trend</h4>
                    <select id="incomeChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select income trend chart type">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>

                <div class="relative h-64">
                    <canvas id="incomeChart" class="w-full h-full"></canvas>
                </div>

                @if (!isset($incomeTrend) || $incomeTrend->isEmpty())
                    <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No income data available.</p>
                @endif
            </div>

            @role('admin')
                <div class="chart-container bg-white dark:bg-gray-800 rounded-xl p-6 shadow col-span-1 lg:col-span-2 transition-all duration-200 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title">Invoice Status Distribution</h4>
                        <select id="invoiceChartType" class="chart-select border rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" aria-label="Select invoice status chart type">
                            <option value="bar">Bar</option>
                            <option value="line">Line</option>
                        </select>
                    </div>

                    <div class="relative h-64">
                        <canvas id="invoiceStatus" class="w-full h-full"></canvas>
                    </div>

                    @if (!isset($invoiceStatuses) || array_sum($invoiceStatuses) == 0)
                        <p class="no-data text-gray-500 dark:text-gray-400 italic text-center py-4">No invoice status data available.</p>
                    @endif
                </div>
            @endrole
        </div>
    </section>
</div>
@endsection


<script>
/* ---------- Robust Dashboard Chart Init ---------- */

/* Normalize and handle many server-side shapes (Collections, arrays, assoc maps) */
function normalizeSeries(raw) {
  try {
    if (!raw) return { labels: [], values: [] };
    // If JSON string
    if (typeof raw === 'string') {
      try { raw = JSON.parse(raw); } catch (e) { return { labels: [], values: [] }; }
    }
    // If object (assoc map like { "2025-07": 100, ... })
    if (!Array.isArray(raw) && typeof raw === 'object') {
      const keys = Object.keys(raw);
      if (!keys.length) return { labels: [], values: [] };
      return { labels: keys, values: keys.map(k => Number(raw[k] ?? 0)) };
    }
    // If array:
    if (Array.isArray(raw)) {
      if (!raw.length) return { labels: [], values: [] };
      const first = raw[0];
      // array of objects [{date:..., value:...}, ...]
      if (first && typeof first === 'object' && !Array.isArray(first)) {
        const labels = raw.map(r => r.date ?? r.label ?? r.name ?? '');
        const values = raw.map(r => Number(r.value ?? r.v ?? r.y ?? Object.values(r).find(v => typeof v === 'number' || !isNaN(Number(v))) ?? 0));
        return { labels, values };
      }
      // array of numbers or strings
      if (typeof first === 'number' || !isNaN(Number(first))) {
        const values = raw.map(v => Number(v ?? 0));
        const labels = values.map((_, i) => i + 1);
        return { labels, values };
      }
      // fallback: convert to labels
      return { labels: raw.map((r,i) => String(r)), values: raw.map(() => 0) };
    }
    return { labels: [], values: [] };
  } catch (err) {
    console.error('normalizeSeries error', err, raw);
    return { labels: [], values: [] };
  }
}

/* ---------- Embed server-provided variables (fallbacks included) ---------- */
const RAW = {
  eggProduction: @json($eggTrend ?? $eggProduction ?? $eggProduction ?? []),
  feedConsumption: @json($feedTrend ?? $feedConsumption ?? []),
  salesData: @json($salesTrend ?? $salesData ?? []),
  incomeData: @json($incomeTrend ?? $incomeData ?? []),
  invoiceStatuses: @json($invoiceStatuses ?? $invoiceStatusesAssoc ?? $invoiceStatusesAssoc ?? []),
  monthlyIncome: @json($monthlyIncome ?? []),
  pendingTransactionsTrend: @json($pendingTransactionsTrend ?? []),
  totalTransactionAmountTrend: @json($totalTransactionAmountTrend ?? []),
  salesComparison: @json($salesComparison ?? []),
  mortalityTrend: @json($mortalityTrend ?? []),
  expenseData: @json($expenseData ?? []),
  profitTrend: @json($profitTrend ?? []),
  profitValue: Number(@json($profit ?? 0)),
  // single-value fallbacks shown in template
  eggSales: Number(@json($eggSales ?? 0)),
  birdSales: Number(@json($birdSales ?? 0)),
  pendingTransactions: Number(@json($pendingTransactions ?? 0)),
  totalTransactionAmount: Number(@json($totalTransactionAmount ?? 0)),
  totalOrderAmount: Number(@json($totalOrderAmount ?? 0))
};

console.log('RAW chart payload:', RAW);

/* Color tokens */
const COLORS = {
  green: '#10B981',
  blue: '#3B82F6',
  red: '#EF4444',
  yellow: '#F59E0B',
  purple: '#7C3AED',
  grayText: '#374151'
};

/* Safe chart creation helper */
function safeCreateChart(canvasId, type, labels, data, datasetOptions = {}, extraOptions = {}) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) {
    console.warn(`Canvas #${canvasId} not found - skipped.`);
    return null;
  }
  const ctx = canvas.getContext('2d');
  return new Chart(ctx, {
    type: type,
    data: {
      labels: labels,
      datasets: [{
        label: datasetOptions.label || '',
        data: data,
        borderColor: datasetOptions.borderColor || COLORS.green,
        backgroundColor: datasetOptions.backgroundColor || 'rgba(16,185,129,0.12)',
        fill: datasetOptions.fill ?? false,
        tension: datasetOptions.tension ?? 0.2,
        ...datasetOptions
      }]
    },
    options: Object.assign({
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: datasetOptions.hideLegend ? false : true, labels: { color: COLORS.grayText } },
        tooltip: { enabled: datasetOptions.hideTooltip ? false : true }
      },
      scales: {
        x: { ticks: { color: COLORS.grayText }, grid: { color: 'rgba(0,0,0,0.04)' } },
        y: { ticks: { color: COLORS.grayText }, grid: { color: 'rgba(0,0,0,0.04)' } }
      }
    }, extraOptions)
  });
}

/* ---------- DOM ready: create charts and wire controls ---------- */
document.addEventListener('DOMContentLoaded', function () {
  // normalize inputs
  const egg = normalizeSeries(RAW.eggProduction);
  const feed = normalizeSeries(RAW.feedConsumption);
  const sales = normalizeSeries(RAW.salesData);
  const income = normalizeSeries(RAW.incomeData);
  // invoice (assoc or array)
  let invoiceLabels = [], invoiceValues = [];
  if (Array.isArray(RAW.invoiceStatuses) && RAW.invoiceStatuses.length) {
    // array of objects or numbers
    const tmp = normalizeSeries(RAW.invoiceStatuses);
    invoiceLabels = tmp.labels; invoiceValues = tmp.values;
  } else if (RAW.invoiceStatuses && typeof RAW.invoiceStatuses === 'object') {
    invoiceLabels = Object.keys(RAW.invoiceStatuses);
    invoiceValues = Object.values(RAW.invoiceStatuses).map(v => Number(v || 0));
  }

  const monthly = (function(){
    if (!RAW.monthlyIncome) return { labels: [], values: [] };
    if (Array.isArray(RAW.monthlyIncome)) return normalizeSeries(RAW.monthlyIncome);
    if (typeof RAW.monthlyIncome === 'object') return { labels: Object.keys(RAW.monthlyIncome), values: Object.values(RAW.monthlyIncome).map(v => Number(v || 0)) };
    return normalizeSeries(RAW.monthlyIncome);
  })();

  const pendingTx = normalizeSeries(RAW.pendingTransactionsTrend);
  const totalTx = normalizeSeries(RAW.totalTransactionAmountTrend);
  const salesCompRaw = RAW.salesComparison || [];
  let salesCompLabels = [], salesCompEgg = [], salesCompBird = [];
  if (Array.isArray(salesCompRaw) && salesCompRaw.length) {
    salesCompLabels = salesCompRaw.map(r => r.date ?? '');
    salesCompEgg = salesCompRaw.map(r => Number(r.egg_sales ?? r.eggSales ?? r.eggs ?? 0));
    salesCompBird = salesCompRaw.map(r => Number(r.bird_sales ?? r.birdSales ?? r.birds ?? 0));
  } else {
    const sc = normalizeSeries(RAW.salesComparison);
    salesCompLabels = sc.labels; salesCompEgg = sc.values; salesCompBird = [];
  }
  const mortality = normalizeSeries(RAW.mortalityTrend);
  const expenseMini = normalizeSeries(RAW.expenseData);
  const profitMini = normalizeSeries(RAW.profitTrend);

  // create charts (only if their canvas exists)
  window.eggChart = safeCreateChart('eggTrend', 'line', egg.labels, egg.values, { label: 'Egg Crates', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });
  window.feedChart = safeCreateChart('feedTrend', 'line', feed.labels, feed.values, { label: 'Feed (kg)', borderColor: COLORS.blue, backgroundColor: 'rgba(59,130,246,0.12)' });
  window.salesChart = safeCreateChart('salesTrend', 'line', sales.labels, sales.values, { label: 'Sales', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });
  window.incomeChart = safeCreateChart('incomeChart', 'line', income.labels, income.values, { label: 'Income', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });

  if (document.getElementById('invoiceStatus')) {
    window.invoiceChart = new Chart(document.getElementById('invoiceStatus').getContext('2d'), {
      type: 'bar',
      data: { labels: invoiceLabels, datasets: [{ label: 'Invoices', data: invoiceValues, backgroundColor: [COLORS.yellow, COLORS.green, COLORS.purple, COLORS.red] }] },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
  }

  window.monthlyIncomeChart = safeCreateChart('monthlyIncomeChart', 'line', monthly.labels, monthly.values, { label: 'Monthly Income', borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' });

  window.pendingTxChart = safeCreateChart('pendingTransactionsTrend', 'line', pendingTx.labels, pendingTx.values, { label: 'Pending Tx', borderColor: COLORS.yellow, backgroundColor: 'rgba(245,158,11,0.12)', hideLegend: true, hideTooltip: true }, { scales: { x: { display: false }, y: { display: false } } });
  window.totalTxChart = safeCreateChart('totalTransactionAmountTrend', 'line', totalTx.labels, totalTx.values, { label: 'Total Tx', borderColor: COLORS.blue, backgroundColor: 'rgba(59,130,246,0.12)', hideLegend: true, hideTooltip: true }, { scales: { x: { display: false }, y: { display: false } } });

  if (document.getElementById('salesComparison')) {
    window.salesComparisonChart = new Chart(document.getElementById('salesComparison').getContext('2d'), {
      type: 'line',
      data: { labels: salesCompLabels, datasets: [
        { label: 'Egg Sales', data: salesCompEgg, borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)', fill: false },
        { label: 'Bird Sales', data: salesCompBird, borderColor: COLORS.blue, backgroundColor: 'rgba(59,130,246,0.12)', fill: false }
      ]},
      options: { responsive: true, maintainAspectRatio: false }
    });
  }

  window.mortalityChart = safeCreateChart('mortalityTrend', 'line', mortality.labels, mortality.values, { label: 'Mortality', borderColor: COLORS.red, backgroundColor: 'rgba(239,68,68,0.12)' });

  window.expensesMini = safeCreateChart('expensesTrend', 'line', expenseMini.labels, expenseMini.values, { hideLegend: true, hideTooltip: true, borderColor: COLORS.red, backgroundColor: 'rgba(239,68,68,0.12)' }, { scales: { x: { display: false }, y: { display: false } } });
  window.incomeMini = safeCreateChart('incomeTrend', 'line', income.labels, income.values, { hideLegend: true, hideTooltip: true, borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)' }, { scales: { x: { display: false }, y: { display: false } } });
  window.profitMini = safeCreateChart('profitMiniChart', 'line', profitMini.labels, profitMini.values, { hideLegend: true, hideTooltip: true, borderColor: (RAW.profitValue >= 0 ? COLORS.green : COLORS.red), backgroundColor: (RAW.profitValue >= 0 ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)') }, { scales: { x: { display: false }, y: { display: false } } });

  /* Map for toggles (chart type selects) */
  const chartMap = {
    eggChartType: window.eggChart,
    feedChartType: window.feedChart,
    salesChartType: window.salesChart,
    incomeChartType: window.incomeChart,
    invoiceChartType: window.invoiceChart,
    monthlyIncomeChartType: window.monthlyIncomeChart,
    salesComparisonChartType: window.salesComparisonChart,
    mortalityTrendChartType: window.mortalityChart,
    // Note: pending/total transaction mini-charts don't have selects in this template
  };

  /* Wire selects to change chart type */
  Object.keys(chartMap).forEach(selectId => {
    const sel = document.getElementById(selectId);
    if (!sel) return;
    sel.addEventListener('change', (e) => {
      const chart = chartMap[selectId];
      if (!chart) return;
      chart.config.type = e.target.value;
      chart.update();
    });
  });

  /* Tabs for recent sales */
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      // deactivate all buttons
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('border-b-2','border-green-500'));
      // hide all tab-content sections (IDs used in your template)
      document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
      const tab = this.dataset.tab;
      // highlight
      this.classList.add('border-b-2','border-green-500');
      // show content matching id mapping
      if (tab === 'egg') { document.getElementById('egg-sales')?.classList.remove('hidden'); }
      if (tab === 'bird') { document.getElementById('bird-sales')?.classList.remove('hidden'); }
    });
  });

  /* Dark mode observer - update tick/legend color */
  function applyDarkToCharts(isDark) {
    const textColor = isDark ? '#D1D5DB' : '#374151';
    const grid = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
    [window.eggChart, window.feedChart, window.salesChart, window.incomeChart, window.invoiceChart, window.monthlyIncomeChart, window.salesComparisonChart, window.mortalityChart, window.expensesMini, window.incomeMini, window.profitMini, window.pendingTxChart, window.totalTxChart].forEach(c => {
      if (!c) return;
      if (!c.options.scales) c.options.scales = {};
      c.options.scales.x = c.options.scales.x || {};
      c.options.scales.y = c.options.scales.y || {};
      c.options.scales.x.ticks = c.options.scales.x.ticks || {};
      c.options.scales.y.ticks = c.options.scales.y.ticks || {};
      c.options.scales.x.ticks.color = textColor;
      c.options.scales.y.ticks.color = textColor;
      c.options.scales.x.grid = c.options.scales.x.grid || {};
      c.options.scales.y.grid = c.options.scales.y.grid || {};
      c.options.scales.x.grid.color = grid;
      c.options.scales.y.grid.color = grid;
      if (c.options.plugins && c.options.plugins.legend && c.options.plugins.legend.labels) {
        c.options.plugins.legend.labels.color = textColor;
      }
      c.update();
    });
  }
  applyDarkToCharts(document.documentElement.classList.contains('dark') || document.body.classList.contains('dark'));
  const observer = new MutationObserver(() => applyDarkToCharts(document.documentElement.classList.contains('dark') || document.body.classList.contains('dark')));
  observer.observe(document.documentElement, { attributes: true });

}); // DOMContentLoaded


// reminder WIDGET

document.addEventListener('DOMContentLoaded', () => {
        const reminders = document.querySelectorAll('#reminder-rotator [data-reminder]');
        const dots = document.querySelectorAll('.reminder-dot');
        const prevBtn = document.getElementById('prev-reminder');
        const nextBtn = document.getElementById('next-reminder');
        const count = document.getElementById('reminder-count');
        let index = 0;
        let interval = setInterval(nextReminder, 5000);

        function showReminder(i, direction = 'next') {
            reminders.forEach((el, j) => {
                el.classList.remove('rotate-x-0', 'opacity-100', 'rotate-x-90', '-rotate-x-90', 'opacity-0');
                if (j === i) {
                    el.classList.add('rotate-x-0', 'opacity-100');
                } else {
                    el.classList.add(direction === 'next' ? 'rotate-x-90' : '-rotate-x-90', 'opacity-0');
                }
            });
            dots.forEach((dot, j) => {
                dot.classList.toggle('bg-blue-600', j === i);
                dot.classList.toggle('bg-gray-400', j !== i);
            });
        }

        function nextReminder() {
            index = (index + 1) % reminders.length;
            showReminder(index, 'next');
        }

        function prevReminder() {
            index = (index - 1 + reminders.length) % reminders.length;
            showReminder(index, 'prev');
        }

        // Pause on hover
        const rotator = document.getElementById('reminder-rotator');
        rotator.addEventListener('mouseenter', () => clearInterval(interval));
        rotator.addEventListener('mouseleave', () => interval = setInterval(nextReminder, 5000));

        // Navigation buttons
        nextBtn.addEventListener('click', () => {
            clearInterval(interval);
            nextReminder();
            interval = setInterval(nextReminder, 5000);
        });

        prevBtn.addEventListener('click', () => {
            clearInterval(interval);
            prevReminder();
            interval = setInterval(nextReminder, 5000);
        });

        // Dot navigation
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const newIndex = parseInt(dot.dataset.index);
                const direction = newIndex > index ? 'next' : 'prev';
                index = newIndex;
                clearInterval(interval);
                showReminder(index, direction);
                interval = setInterval(nextReminder, 5000);
            });
        });

        // Mark as Completed
        document.querySelectorAll('.mark-read-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                fetch(`/alerts/mark-read/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const reminder = reminders[index];
                        reminder.remove();
                        dots[index].remove();
                        const remaining = document.querySelectorAll('#reminder-rotator [data-reminder]');
                        count.textContent = `${remaining.length} pending`;
                        if (remaining.length) {
                            index = index % remaining.length;
                            showReminder(index);
                        } else {
                            rotator.innerHTML = '<p class="text-xs text-gray-600 dark:text-gray-400 text-center py-8">No pending tasks</p>';
                            clearInterval(interval);
                        }
                    }
                })
                .catch(err => console.error('Error:', err));
            });
        });
    });
       

// WEATHER
   document.addEventListener("DOMContentLoaded", () => {
    const condition = document.getElementById("weather-condition").innerText.toLowerCase();
    const iconEl = document.getElementById("weather-icon");

    let iconSvg = "";

    if (condition.includes("clear")) {
        // ☀️ Sunny
        iconSvg = `
        <svg class="w-8 h-8 text-yellow-400 animate-spin-slow glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364-6.364l-2.121 2.121M8.757 17.243l-2.121 2.121m12.728 0l-2.121-2.121M8.757 6.757l-2.121-2.121" />
        </svg>`;
    } else if (condition.includes("cloud")) {
        // ☁️ Cloudy
        iconSvg = `
        <svg class="w-8 h-8 text-gray-500 animate-bounce-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
        </svg>`;
    } else if (condition.includes("rain")) {
        // 🌧️ Rainy
        iconSvg = `
        <svg class="w-8 h-8 text-blue-500 animate-fade-in-delay" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 13v1m-4 2v1m-4-4v1m0 4v1M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
        </svg>`;
    } else if (condition.includes("snow")) {
        // ❄️ Snow
        iconSvg = `
        <svg class="w-8 h-8 text-blue-300 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m9-9H3m16.95-6.95l-13.9 13.9M6.05 6.05l13.9 13.9" />
        </svg>`;
    } else {
        // Default 🌙
        iconSvg = `
        <svg class="w-8 h-8 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
        </svg>`;
    }

    iconEl.innerHTML = iconSvg;
});
</script>
