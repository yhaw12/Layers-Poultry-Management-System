{{-- invoices.index --}}

{{-- @extends('layouts.app') --}}

{{-- @section('title', 'Invoices')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Invoices</h2>
        <a href="{{ route('sales.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                  dark:bg-blue-500 dark:hover:bg-blue-600 transition">
            ➕ Create Invoice
        </a>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Invoices</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($sales->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Amount</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($sales->sum('total_amount'), 2) }}</p>
                <span class="text-gray-600 dark:text-gray-300">GHS</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Paid</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($sales->sum('paid_amount'), 2) }}</p>
                <span class="text-gray-600 dark:text-gray-300">GHS</span>
            </div>
        </div>
    </section>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif
    <!-- Error Message -->
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-2xl border border-red-200 dark:border-red-700">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Filter Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Invoices</h3>
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <button type="submit" 
                        class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                               dark:bg-blue-500 dark:hover:bg-blue-600 text-sm transition">
                    🔍 Filter
                </button>
            </form>
        </div>
    </section>

    <!-- Invoices Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Invoice Records</h3>
            @if ($sales->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No invoices found yet.</p>
                    <a href="{{ route('sales.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Create Your First Invoice
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Invoice #</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Customer</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Due Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Total (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Paid (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 font-semibold text-blue-600 dark:text-blue-400">{{ $sale->id }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $sale->customer ? $sale->customer->name : 'Unknown' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $sale->due_date ? $sale->due_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS {{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS {{ number_format($sale->paid_amount, 2) }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $sale->status == 'paid' ? 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200' : ($sale->status == 'partially_paid' ? 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200' : ($sale->status == 'overdue' ? 'bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-200' : 'bg-blue-200 text-blue-800 dark:bg-blue-700 dark:text-blue-200')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $sale->status)) }}
                                        </span>
                                    </td>
                                    <td class="p-4 flex space-x-2">
                                        @if (!$sale->isPaid())
                                            <form action="{{ route('sales.recordPayment', $sale) }}" method="POST" class="flex flex-wrap items-end space-x-2 no-print" onsubmit="return confirm('Are you sure you want to record this payment?');">
                                                @csrf
                                                <div class="min-w-[100px]">
                                                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $sale->total_amount - $sale->paid_amount }}" 
                                                           placeholder="Amount" 
                                                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition text-xs py-1 px-2">
                                                </div>
                                                <div class="min-w-[120px]">
                                                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" 
                                                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition text-xs py-1 px-2">
                                                </div>
                                                <div class="min-w-[120px]">
                                                    <select name="payment_method" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition text-xs py-1 px-2">
                                                        <option value="">Select Method</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="bank_transfer">Bank Transfer</option>
                                                        <option value="mobile_money">Mobile Money</option>
                                                    </select>
                                                </div>
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs transition">
                                                    💳 Pay
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('sales.invoice', ['sale' => $sale->id, 'preview' => 1]) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 text-xs transition">
                                           📜 Preview
                                        </a>
                                        <a href="{{ route('sales.invoice', $sale->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs transition">
                                           📥 Download PDF
                                        </a>
                                        @can('email-invoices')
                                            <a href="{{ route('sales.emailInvoice', $sale->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-purple-500 text-white rounded-lg shadow hover:bg-purple-600 text-xs transition">
                                               📧 Email
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if ($sales instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex justify-end">
                        {{ $sales->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection --}}