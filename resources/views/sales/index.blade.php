@extends('layouts.app')

@section('title', 'Sales')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-50 dark:bg-gray-900 dark:text-white">
    <!-- Accessible Toast (global) -->
    <div id="toast" class="fixed right-6 top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4" role="status" aria-live="polite" aria-atomic="true">
        <div id="toastInner" class="max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 text-white flex items-center space-x-3" tabindex="-1">
            <svg id="toastIcon" class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="toastMessage" class="sr-only">Notification</span>
        </div>
    </div>

    <!-- Header -->
    <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Sales & Invoices</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage invoices, quick payments and exports — improved for speed and accessibility.</p>
        </div>

        <div class="flex space-x-4">
            <button id="openPendingPaymentsBtn" 
                    class="inline-flex items-center bg-yellow-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-yellow-600 
                           dark:bg-yellow-400 dark:hover:bg-yellow-500 transition-colors duration-200 font-medium"
                    aria-controls="pendingPaymentsModal" aria-expanded="false">
                💰 Pending Payments <span id="unpaidCount" class="ml-2 bg-white dark:bg-gray-800 text-yellow-600 dark:text-yellow-300 rounded-full px-2.5 py-1 text-xs font-semibold">{{ $sales->where('status', '!=', 'paid')->count() }}</span>
            </button>

            <a href="{{ route('sales.create') }}" id="addSaleBtn"
               class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-blue-600 
                      dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium" aria-label="Add new sale">
                ➕ Add Sale
            </a>
        </div>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center transform transition-all hover:scale-105 duration-200">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ number_format($sales->total(), 0) }}</p>
                <span class="text-sm text-gray-600 dark:text-gray-300 mt-1">Records</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center transform transition-all hover:scale-105 duration-200">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">₵ {{ number_format($sales->sum('total_amount'), 2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center transform transition-all hover:scale-105 duration-200">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">₵ {{ number_format($sales->sum('paid_amount'), 2) }}</p>
            </div>
        </div>
    </section>

    <!-- Success/Error Messages (server flash) -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/80 text-green-800 dark:text-green-200 rounded-xl border border-green-200 dark:border-green-700/50 backdrop-blur-sm" role="status">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/80 text-red-800 dark:text-red-200 rounded-xl border border-red-200 dark:border-red-700/50 backdrop-blur-sm" role="alert">
            <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter & Quick Search Form -->
    <section>
        <div class="container-box bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Filter Sales</h3>
            <form method="GET" class="flex flex-wrap items-end gap-4" id="filterForm">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}"
                           class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}" 
                           class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>

                <!-- Quick client-side text search -->
                <div class="flex-1 min-w-[220px]">
                    <label for="quickSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quick search</label>
                    <input id="quickSearch" type="search" placeholder="Customer, invoice #, product..." class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" aria-label="Quick search sales">
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-blue-600 dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Filter
                    </button>
                    <button id="resetFilters" type="button" class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Reset</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Sales Table -->
    <section>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Sales Records</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Showing <strong id="visibleCount">{{ $sales->count() }}</strong> of <strong>{{ $sales->total() }}</strong></div>
            </div>

            @if ($sales->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">No sales records found yet.</p>
                    <a href="{{ route('sales.create') }}" 
                       class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-blue-600 
                              dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium">
                        ➕ Add Your First Sale
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm" role="table" aria-label="Sales table">
                        <thead class="sticky top-0">
                            <tr class="bg-gray-100 dark:bg-gray-700/80">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Invoice #</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Product</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Quantity</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total (₵)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Paid (₵)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody" class="divide-y divide-gray-200 dark:divide-gray-600" role="rowgroup">
                            @foreach ($sales as $sale)
                                <tr data-sale-id="{{ $sale->id }}" class="sale-row hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-150" role="row">
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400" role="cell">{{ $sale->id }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300" role="cell">{{ $sale->customer ? $sale->customer->name : 'Unknown' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300" role="cell">
                                        @if ($sale->saleable_type == 'App\\Models\\Bird' || $sale->saleable_type == \App\Models\Bird::class)
                                            {{ optional($sale->saleable)->breed ?? 'Bird' }} {{ optional($sale->saleable)->type ? '(' . $sale->saleable->type . ')' : '' }}
                                        @else
                                            {{ $sale->saleable ? 'Eggs' : 'Unknown Product' }}
                                        @endif
                                    </td>
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400" role="cell">{{ $sale->quantity }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300" role="cell">₵ <span class="sale-total-amount">{{ number_format($sale->total_amount, 2) }}</span></td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300" role="cell">₵ <span class="sale-paid-amount">{{ number_format($sale->paid_amount, 2) }}</span></td>
                                    <td class="p-4" role="cell">
                                        @php
                                            $status = $sale->status;
                                            $statusText = ucfirst(str_replace('_', ' ', $status));
                                        @endphp
                                        <span class="sale-status px-2.5 py-1 text-xs font-semibold rounded-full
                                            {{ $status == 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-700/80 dark:text-green-200' : ($status == 'partially_paid' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700/80 dark:text-yellow-200' : ($status == 'overdue' ? 'bg-red-100 text-red-800 dark:bg-red-700/80 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-700/80 dark:text-blue-200')) }}" aria-label="Status: {{ $statusText }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300" role="cell">{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="p-4 flex flex-wrap gap-2" role="cell">
                                        @php $isPaid = round((float)$sale->paid_amount, 2) >= round((float)$sale->total_amount, 2); @endphp
                                        @if (! $isPaid)
                                            <button data-action="open-pay-modal"
                                                    data-sale-id="{{ $sale->id }}"
                                                    data-paid="{{ number_format($sale->paid_amount, 2, '.', '') }}"
                                                    data-total="{{ number_format($sale->total_amount, 2, '.', '') }}"
                                                    class="pay-btn inline-flex items-center px-3 py-1.5 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 text-xs transition-colors duration-200 font-medium"
                                                    aria-haspopup="dialog" aria-controls="paymentModal" aria-label="Pay invoice {{ $sale->id }}">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                Pay
                                            </button>
                                        @endif

                                        <a href="{{ route('sales.invoice', $sale->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 text-xs transition-colors duration-200 font-medium" aria-label="Download invoice {{ $sale->id }}">
                                           <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                           Download
                                        </a>

                                        @can('email-invoices')
                                            <a href="{{ route('sales.emailInvoice', $sale->id) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-500 text-white rounded-lg shadow-md hover:bg-purple-600 text-xs transition-colors duration-200 font-medium" aria-label="Email invoice {{ $sale->id }}">
                                               <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                               Email
                                            </a>
                                        @endcan

                                        <a href="{{ route('sales.edit', $sale) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 text-xs transition-colors duration-200 font-medium" aria-label="Edit invoice {{ $sale->id }}">
                                           <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                           Edit
                                        </a>

                                        <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 text-xs transition-colors duration-200 font-medium" aria-label="Delete invoice {{ $sale->id }}">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M7 7h10"></path></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination + Prev/Next -->
                @if ($sales instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button id="prevPageBtn" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled>← Previous</button>
                            <button id="nextPageBtn" class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled>Next →</button>
                        </div>
                        <div class="flex justify-end">
                            {{ $sales->links('pagination::tailwind') }}
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>

    <!-- Pending Payments Modal -->
    <div id="pendingPaymentsModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="pendingModalTitle">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="pendingModalInner">
            <div class="flex justify-between items-center mb-4">
                <h3 id="pendingModalTitle" class="text-xl font-semibold text-gray-800 dark:text-gray-200">Pending Payments</h3>
                <button id="closePendingPaymentsBtn" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors duration-200" aria-label="Close pending payments">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="mb-4">
                <label for="paymentStatusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Status</label>
                <select id="paymentStatusFilter" onchange="loadPendingPayments()" class="w-full max-w-[200px] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-400/30 transition-colors duration-200">
                    <option value="">All Unpaid</option>
                    <option value="pending">Pending</option>
                    <option value="partially_paid">Partially Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div id="pendingPaymentsContent" class="overflow-x-auto">
                <div class="text-center py-8" id="pendingSpinner">
                    <svg class="animate-spin h-8 w-8 mx-auto text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Loading pending payments…</p>
                </div>
                <table id="pendingPaymentsTableWrapper" class="w-full border-collapse text-sm hidden">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700/80">
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Invoice #</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total (₵)</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Paid (₵)</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Balance (₵)</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Due Date</th>
                            <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody id="pendingPaymentsTable" class="divide-y divide-gray-200 dark:divide-gray-600"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal (unchanged structure) -->
    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="paymentModalInner">
            <div class="flex justify-between items-center mb-4">
                <h3 id="paymentModalTitle" class="text-xl font-semibold text-gray-800 dark:text-gray-200">Record Payment for Invoice #<span id="paymentSaleId"></span></h3>
                <button id="closePaymentBtn" class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors duration-200" aria-label="Close payment modal">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="paymentForm" method="POST" onsubmit="return handlePaymentSubmit(event)">
                @csrf
                <div class="mb-4">
                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (₵)</label>
                    <input type="number" id="payment_amount" name="amount" step="0.01" min="0.01" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-400/30 transition-colors duration-200" required>
                    <span id="paymentMaxAmount" class="text-xs text-gray-500 dark:text-gray-400 mt-1 block"></span>
                </div>
                <div class="mb-4">
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ now()->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-400/30 transition-colors duration-200" required>
                </div>
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-400/30 transition-colors duration-200">
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div id="paymentError" class="hidden mb-4 p-2 bg-red-100 dark:bg-red-900/80 text-red-800 dark:text-red-200 rounded-lg flex items-center space-x-2">
                    <svg class="h-4 w-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span></span>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancelPaymentBtn" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600/80 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors duration-200 font-medium">Cancel</button>
                    <button type="submit" id="submitPaymentBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition-colors duration-200 font-medium inline-flex items-center" aria-busy="false">
                        <svg id="submitSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <span id="submitPaymentLabel">💳 Record Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal (centered like pending) -->
    <div id="deleteModalSales" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 transition-opacity duration-300" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg max-w-lg w-full p-6 transform transition-all duration-300 scale-95 opacity-0" id="deleteModalInner">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to delete this sale record? This action cannot be undone.</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="deleteCancelSales" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700">Cancel</button>
                <button id="deleteConfirmSales" class="px-4 py-2 rounded bg-red-600 text-white text-sm font-medium hover:bg-red-700" disabled>
                    <span class="flex items-center">
                        <svg id="deleteSpinnerSales" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                        Delete
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Confirmation Modal (centered like pending) -->
    <div id="editModalSales" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 transition-opacity duration-300" aria-modal="true" role="dialog">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg max-w-lg w-full p-6 transform transition-all duration-300 scale-95 opacity-0" id="editModalInner">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Confirm Edit</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Are you sure you want to edit this sale record?</p>
            <div class="mt-4 flex justify-end gap-2">
                <button id="editCancelSales" class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700">Cancel</button>
                <button id="editConfirmSales" class="px-4 py-2 rounded bg-yellow-500 text-white text-sm font-medium hover:bg-yellow-600">
                    <span class="flex items-center">
                        <svg id="editSpinnerSales" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                        Edit
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal animation for open state */
    #pendingPaymentsModal:not(.hidden) #pendingModalInner,
    #paymentModal:not(.hidden) #paymentModalInner,
    #deleteModalSales:not(.hidden) #deleteModalInner,
    #editModalSales:not(.hidden) #editModalInner {
        transform: scale(1);
        opacity: 1;
    }

    /* Improve table readability */
    table tr { transition: background-color 0.15s ease; }
    table th, table td { vertical-align: middle; }

    /* Focus styles for accessibility */
    button:focus, a:focus, input:focus, select:focus { outline: 2px solid #3b82f6; outline-offset: 2px; }

    /* Toast animation */
    #toast:not(.invisible) { transform: translateY(0); opacity: 1; }

    /* Make header sticky for long lists */
    thead.sticky { position: sticky; top: 0; z-index: 10; }
</style>

<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ----- tiny helpers -----
    function formatCurrency(num) { return (Number(num) || 0).toFixed(2); }

    function showToast(message, type = 'success', timeout = 3500) {
        const toast = document.getElementById('toast');
        const inner = document.getElementById('toastInner');
        const msgEl = document.getElementById('toastMessage');
        const icon = document.getElementById('toastIcon');

        msgEl.innerText = message;
        msgEl.classList.remove('sr-only');

        // style by type
        inner.className = 'max-w-sm rounded-xl p-4 shadow-xl flex items-center space-x-3';
        if (type === 'success') inner.classList.add('bg-green-800', 'text-white');
        else if (type === 'error') inner.classList.add('bg-red-800', 'text-white');
        else inner.classList.add('bg-gray-800', 'text-white');

        icon.className = 'h-5 w-5';
        if (type === 'success') icon.classList.add('text-green-300');
        else if (type === 'error') icon.classList.add('text-red-300');
        else icon.classList.add('text-gray-300');

        toast.classList.remove('invisible', 'opacity-0');
        toast.classList.add('opacity-100');
        toast.style.pointerEvents = 'auto';

        // auto-hide
        clearTimeout(window._toastTimeout);
        window._toastTimeout = setTimeout(() => {
            toast.classList.add('opacity-0');
            toast.style.pointerEvents = 'none';
            setTimeout(() => toast.classList.add('invisible'), 250);
        }, timeout);
    }

    // ----- Modal helpers -----
    function openModal(modal, invokingEl) {
        if (!modal) return;
        modal.__invoker = invokingEl || document.activeElement;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (modal && modal.__invoker && typeof modal.__invoker.focus === 'function') modal.__invoker.focus();
    }

    // Global Escape to close visible modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            ['pendingPaymentsModal', 'paymentModal', 'deleteModalSales', 'editModalSales'].forEach(id => {
                const m = document.getElementById(id);
                if (m && !m.classList.contains('hidden')) closeModal(m);
            });
        }
    });

    // ----- Quick client-side search (debounced) -----
    const quickSearch = document.getElementById('quickSearch');
    let searchTimer = null;
    function applyQuickSearch() {
        const q = quickSearch.value.trim().toLowerCase();
        const rows = Array.from(document.querySelectorAll('#salesTableBody tr'));
        let visible = 0;
        rows.forEach(r => {
            if (!q) { r.style.display = ''; visible++; return; }
            const text = (r.innerText || '').toLowerCase();
            if (text.includes(q)) { r.style.display = ''; visible++; } else { r.style.display = 'none'; }
        });
        document.getElementById('visibleCount').innerText = visible;
    }
    if (quickSearch) {
        quickSearch.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyQuickSearch, 200);
        });
    }

    // ----- Payment modal handling -----
    function openPaymentModal(saleId, paid, total, invoker) {
        const modal = document.getElementById('paymentModal');
        if (!modal) return;
        document.getElementById('paymentSaleId').innerText = saleId;
        modal.dataset.saleId = saleId;

        const paidNum = parseFloat(paid || 0);
        const totalNum = parseFloat(total || 0);
        const balance = Math.max(0, (totalNum - paidNum));

        const amountInput = document.getElementById('payment_amount');
        amountInput.value = balance.toFixed(2);
        amountInput.max = balance.toFixed(2);
        document.getElementById('paymentMaxAmount').innerText = 'Outstanding balance: ₵ ' + balance.toFixed(2);

        document.getElementById('paymentError').classList.add('hidden');
        openModal(modal, invoker);
        setTimeout(() => amountInput.focus(), 120);
    }

    function closePaymentModal() { closeModal(document.getElementById('paymentModal')); }

    // ----- Payment submit (AJAX) -----
    let paymentSubmitting = false;
    async function handlePaymentSubmit(e) {
        e.preventDefault();
        if (paymentSubmitting) return false;

        const modal = document.getElementById('paymentModal');
        const saleId = modal && modal.dataset.saleId;
        if (!saleId) return false;

        const amountInput = document.getElementById('payment_amount');
        const amount = parseFloat(amountInput.value || 0);
        const date = document.getElementById('payment_date').value;
        const method = document.getElementById('payment_method').value;
        const errorEl = document.getElementById('paymentError');
        const submitBtn = document.getElementById('submitPaymentBtn');
        const spinner = document.getElementById('submitSpinner');
        const label = document.getElementById('submitPaymentLabel');

        errorEl.classList.add('hidden');
        errorEl.querySelector('span').innerText = '';

        paymentSubmitting = true;
        submitBtn.setAttribute('disabled', 'disabled');
        submitBtn.setAttribute('aria-busy', 'true');
        spinner.classList.remove('hidden');
        label.innerText = 'Recording…';

        const url = `/sales/${saleId}/record-payment`;
        const payload = { amount: amount, payment_date: date, payment_method: method || null };

        try {
            const res = await fetch(url, {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            });

            let data;
            try { data = await res.json(); } catch (e) { throw new Error('Server did not return JSON.'); }

            if (!res.ok) {
                if (data && data.errors) {
                    const messages = Object.values(data.errors).flat().join(' ');
                    errorEl.querySelector('span').innerText = messages;
                } else if (data && data.error) {
                    errorEl.querySelector('span').innerText = data.error;
                } else {
                    errorEl.querySelector('span').innerText = 'Failed to record payment.';
                }
                errorEl.classList.remove('hidden');
                return false;
            }

            const saleRow = document.querySelector(`tr[data-sale-id="${saleId}"]`);
            if (saleRow) {
                const paidEl = saleRow.querySelector('.sale-paid-amount'); if (paidEl) paidEl.innerText = formatCurrency(data.paid_amount);
                const statusEl = saleRow.querySelector('.sale-status');
                if (statusEl) {
                    statusEl.innerText = data.status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
                    statusEl.className = 'sale-status px-2 py-1 text-xs font-semibold rounded-full';
                    if (data.status === 'paid') statusEl.classList.add('bg-green-100','text-green-800','dark:bg-green-700/80','dark:text-green-200');
                    else if (data.status === 'partially_paid') statusEl.classList.add('bg-yellow-100','text-yellow-800','dark:bg-yellow-700/80','dark:text-yellow-200');
                    else if (data.status === 'overdue') statusEl.classList.add('bg-red-100','text-red-800','dark:bg-red-700/80','dark:text-red-200');
                    else statusEl.classList.add('bg-blue-100','text-blue-800','dark:bg-blue-700/80','dark:text-blue-200');
                }
                const payBtn = saleRow.querySelector('[data-action="open-pay-modal"]'); if (data.status === 'paid' && payBtn) payBtn.remove();
            }

            updateUnpaidCount();
            closePaymentModal();
            showToast(data.message || 'Payment recorded.', 'success');
        } catch (err) {
            console.error('Payment request error', err);
            errorEl.querySelector('span').innerText = err.message || 'An unexpected error occurred.';
            errorEl.classList.remove('hidden');
        } finally {
            paymentSubmitting = false;
            submitBtn.removeAttribute('disabled');
            submitBtn.setAttribute('aria-busy', 'false');
            spinner.classList.add('hidden');
            label.innerText = '💳 Record Payment';
        }

        return false;
    }

    // ----- Pending payments (client-side) -----
    async function loadPendingPayments() {
        const filter = document.getElementById('paymentStatusFilter').value;
        const tableBody = document.getElementById('pendingPaymentsTable');
        const spinner = document.getElementById('pendingSpinner');
        const wrapper = document.getElementById('pendingPaymentsTableWrapper');

        spinner.classList.remove('hidden'); wrapper.classList.add('hidden'); tableBody.innerHTML = '';
        await new Promise(r => setTimeout(r, 150)); // tiny UX delay

        const rows = Array.from(document.querySelectorAll('tr[data-sale-id]'));
        const pending = [];
        rows.forEach(r => {
            const saleId = r.dataset.saleId;
            const cust = r.querySelector('td:nth-child(2)')?.innerText || '';
            const totalText = r.querySelector('.sale-total-amount')?.innerText || '0';
            const paidText = r.querySelector('.sale-paid-amount')?.innerText || '0';
            const statusEl = r.querySelector('.sale-status');
            const status = statusEl ? statusEl.innerText.toLowerCase() : '';
            const due = r.querySelector('td:nth-child(8)')?.innerText || '';
            const total = parseFloat(totalText.replace(/,/g, '')) || 0;
            const paid = parseFloat(paidText.replace(/,/g, '')) || 0;
            const balance = (total - paid).toFixed(2);
            if (status !== 'paid') {
                if (!filter || status.includes(filter.replace('_', ' '))) {
                    pending.push({ saleId, cust, total, paid, balance, status, due });
                }
            }
        });

        if (pending.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" class="p-4 text-center">No pending payments found.</td></tr>';
            spinner.classList.add('hidden'); wrapper.classList.remove('hidden'); return;
        }

        tableBody.innerHTML = pending.map(p => `\
            <tr>\
                <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">${p.saleId}</td>\
                <td class="p-4">${p.cust}</td>\
                <td class="p-4">₵ ${parseFloat(p.total).toFixed(2)}</td>\
                <td class="p-4">₵ ${parseFloat(p.paid).toFixed(2)}</td>\
                <td class="p-4">₵ ${parseFloat(p.balance).toFixed(2)}</td>\
                <td class="p-4">${p.status}</td>\
                <td class="p-4">${p.due}</td>\
                <td class="p-4">\
                    <button data-action="open-pay-modal" data-sale-id="${p.saleId}" data-paid="${p.paid}" data-total="${p.total}" class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs transition font-medium">\
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Pay\
                    </button>\
                </td>\
            </tr>\
        `).join('');

        spinner.classList.add('hidden'); wrapper.classList.remove('hidden');
    }

    // ----- Delete & Edit modal wiring -----
    let pendingDeleteForm = null; let pendingEditUrl = null;
    function bindDeleteForms() {
        const forms = Array.from(document.querySelectorAll('#salesTableBody .delete-form'));
        forms.forEach(form => {
            if (form.__deleteBound) return;
            form.addEventListener('submit', function (ev) {
                ev.preventDefault(); pendingDeleteForm = form;
                const deleteConfirm = document.getElementById('deleteConfirmSales'); deleteConfirm.removeAttribute('disabled');
                openModal(document.getElementById('deleteModalSales'), form.querySelector('button'));
                deleteConfirm.focus();
            }); form.__deleteBound = true;
        });
    }
    function bindEditLinks() {
        const links = Array.from(document.querySelectorAll('#salesTableBody a[href*="/sales/"][href$="/edit"]'));
        links.forEach(a => { if (a.__editBound) return; a.addEventListener('click', function (ev) { ev.preventDefault(); pendingEditUrl = a.getAttribute('href'); openModal(document.getElementById('editModalSales'), a); document.getElementById('editConfirmSales').focus(); }); a.__editBound = true; });
    }

    async function refreshSalesTable(url) {
        try {
            const res = await fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Failed to fetch table (status ' + res.status + ')');
            const html = await res.text(); const parser = new DOMParser(); const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.querySelector('#salesTableBody'); if (newTbody) document.getElementById('salesTableBody').innerHTML = newTbody.innerHTML;
            const newPager = doc.querySelector('.mt-6.flex.items-center.justify-between, .mt-6.flex.justify-end'); const oldPager = document.querySelector('.mt-6.flex.items-center.justify-between, .mt-6.flex.justify-end');
            if (newPager && oldPager) oldPager.innerHTML = newPager.innerHTML;
            else if (newPager && !oldPager) { const container = document.querySelector('section > .bg-white') || document.body; container.insertAdjacentElement('beforeend', newPager.cloneNode(true)); }
            bindAllTableControls();
            updatePrevNextButtons();
        } catch (err) { console.error('refreshSalesTable error', err); showToast('Failed to refresh table. Reloading page...', 'error'); setTimeout(() => window.location.reload(), 900); }
    }

    function bindAllTableControls() { bindDeleteForms(); bindEditLinks(); updateUnpaidCount(); updatePrevNextButtons(); }

    function updateUnpaidCount() { const rows = Array.from(document.querySelectorAll('tr[data-sale-id]')); const unpaid = rows.filter(r => { const status = r.querySelector('.sale-status')?.innerText?.toLowerCase() || ''; return status !== 'paid'; }).length; const unpaidCountEl = document.getElementById('unpaidCount'); if (unpaidCountEl) unpaidCountEl.innerText = unpaid; }

    // ----- Delegation -----
    document.addEventListener('click', function (e) {
        const payBtn = e.target.closest('[data-action="open-pay-modal"]'); if (payBtn) { const saleId = payBtn.dataset.saleId; const paid = payBtn.dataset.paid; const total = payBtn.dataset.total; openPaymentModal(saleId, paid, total, payBtn); return; }
        const paginationLink = e.target.closest('.mt-6.flex.justify-end a, .pagination a, nav.pagination a, .mt-6.flex.items-center.justify-between a');
        if (paginationLink) { const href = paginationLink.getAttribute('href'); if (href && document.querySelector('#salesTableBody') && !paginationLink.classList.contains('opacity-50') && !paginationLink.hasAttribute('disabled')) { e.preventDefault(); refreshSalesTable(href).then(() => { try { history.pushState({}, '', href); } catch (_) {} }); } }
    });

    // ----- Modal button bindings -----
    document.getElementById('closePaymentBtn')?.addEventListener('click', closePaymentModal);
    document.getElementById('cancelPaymentBtn')?.addEventListener('click', closePaymentModal);
    document.getElementById('closePendingPaymentsBtn')?.addEventListener('click', () => closeModal(document.getElementById('pendingPaymentsModal')));
    document.getElementById('openPendingPaymentsBtn')?.addEventListener('click', function () { const modal = document.getElementById('pendingPaymentsModal'); openModal(modal, this); loadPendingPayments(); });

    document.getElementById('deleteConfirmSales')?.addEventListener('click', async () => {
        if (!pendingDeleteForm) return; const action = pendingDeleteForm.getAttribute('action'); const deleteConfirm = document.getElementById('deleteConfirmSales'); const deleteSpinner = document.getElementById('deleteSpinnerSales'); deleteConfirm.setAttribute('disabled', 'disabled'); deleteSpinner.classList.remove('hidden');
        try {
            const res = await fetch(action, { method: 'DELETE', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            let data = null; try { data = await res.json(); } catch (_) { data = null; }
            if (res.ok) { const row = pendingDeleteForm.closest('tr[data-sale-id]'); if (row) row.remove(); await refreshSalesTable(window.location.href); updateUnpaidCount(); showToast((data && data.message) ? data.message : 'Sale deleted.'); }
            else { const msg = (data && data.message) ? data.message : `Delete failed (status ${res.status})`; showToast(msg, 'error'); console.error('Delete error', msg); }
        } catch (err) { console.error('Delete request failed', err); showToast('Network error while deleting.', 'error'); }
        finally { deleteSpinner.classList.add('hidden'); deleteConfirm.removeAttribute('disabled'); pendingDeleteForm = null; closeModal(document.getElementById('deleteModalSales')); }
    });

    document.getElementById('deleteCancelSales')?.addEventListener('click', () => { pendingDeleteForm = null; closeModal(document.getElementById('deleteModalSales')); });

    document.getElementById('editConfirmSales')?.addEventListener('click', () => { if (!pendingEditUrl) return; const editConfirm = document.getElementById('editConfirmSales'); const editSpinner = document.getElementById('editSpinnerSales'); editConfirm.setAttribute('disabled', 'disabled'); editSpinner.classList.remove('hidden'); window.location.href = pendingEditUrl; });
    document.getElementById('editCancelSales')?.addEventListener('click', () => { pendingEditUrl = null; closeModal(document.getElementById('editModalSales')); });

    // Quick reset filters
    document.getElementById('resetFilters')?.addEventListener('click', function () { document.getElementById('filterForm').reset(); document.getElementById('quickSearch').value = ''; applyQuickSearch(); });

    // Keyboard shortcut: press 'n' to navigate to Add Sale (when not focused inside an input)
    document.addEventListener('keydown', function (e) {
        if (e.key.toLowerCase() === 'n') {
            const active = document.activeElement; if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.isContentEditable)) return; const addBtn = document.getElementById('addSaleBtn'); if (addBtn) addBtn.click();
        }
    });

    // ----- Prev / Next buttons logic -----
    function findPagerLink(relOrLabel) {
        // prefer rel attribute
        const pagerAnchors = Array.from(document.querySelectorAll('.mt-6 a, .pagination a, nav.pagination a'));
        // first try rel attribute
        let found = pagerAnchors.find(a => a.getAttribute('rel') === relOrLabel);
        if (found) return found.getAttribute('href');
        // fallback to aria-label matching 'Previous'/'Next'
        found = pagerAnchors.find(a => (a.getAttribute('aria-label') || '').toLowerCase().includes(relOrLabel.toLowerCase()));
        if (found) return found.getAttribute('href');
        // fallback to link text
        found = pagerAnchors.find(a => (a.innerText || '').toLowerCase().includes(relOrLabel.toLowerCase()));
        if (found) return found.getAttribute('href');
        return null;
    }

    function updatePrevNextButtons() {
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');

        if (!prevBtn || !nextBtn) return;

        const prevHref = findPagerLink('prev') || findPagerLink('previous') || findPagerLink('‹');
        const nextHref = findPagerLink('next') || findPagerLink('›') || findPagerLink('next');

        if (prevHref) {
            prevBtn.removeAttribute('disabled');
            prevBtn.onclick = (e) => { e.preventDefault(); refreshSalesTable(prevHref).then(() => { try { history.pushState({}, '', prevHref); } catch (_) {} }); };
        } else {
            prevBtn.setAttribute('disabled', 'disabled');
            prevBtn.onclick = null;
        }

        if (nextHref) {
            nextBtn.removeAttribute('disabled');
            nextBtn.onclick = (e) => { e.preventDefault(); refreshSalesTable(nextHref).then(() => { try { history.pushState({}, '', nextHref); } catch (_) {} }); };
        } else {
            nextBtn.setAttribute('disabled', 'disabled');
            nextBtn.onclick = null;
        }
    }

    // Initialize
    document.getElementById('paymentForm')?.addEventListener('submit', handlePaymentSubmit);
    document.addEventListener('DOMContentLoaded', () => { bindAllTableControls(); window.addEventListener('popstate', () => refreshSalesTable(window.location.href)); updateUnpaidCount(); updatePrevNextButtons(); });
})();
</script>
@endsection
