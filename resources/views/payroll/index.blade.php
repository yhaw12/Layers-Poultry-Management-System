@extends('layouts.app')

@section('title', 'Payroll Management')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Payroll Management</h2>
        <a href="{{ route('payroll.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                  dark:bg-blue-500 dark:hover:bg-blue-600 transition"
           onclick="return confirm('Add a new payroll record?');">
            ➕ Add Payroll
        </a>
    </section>

    <!-- Summary Cards -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Payroll Records</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($payrolls->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Net Pay</span>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">GHS {{ number_format($payrolls->sum('net_pay'), 2) }}</p>
                <span class="text-gray-600 dark:text-gray-300">GHS</span>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Pending Payments</span>
                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $payrolls->where('status', 'pending')->count() }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
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
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Payroll Records</h3>
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date', $start) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition @error('start_date') border-red-500 @enderror"
                           aria-describedby="start_date-error">
                    @error('start_date')
                        <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date', $end) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition @error('end_date') border-red-500 @enderror"
                           aria-describedby="end_date-error">
                    @error('end_date')
                        <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select id="status" name="status" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                                   dark:bg-blue-500 dark:hover:bg-blue-600 text-sm transition">
                        🔍 Filter
                    </button>
                    <a href="{{ route('payroll.index') }}" 
                       class="inline-flex items-center bg-gray-300 text-gray-800 px-4 py-2 rounded-lg shadow hover:bg-gray-400 
                              dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 text-sm transition">
                        🔄 Reset
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Generate Monthly Payroll -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Generate Monthly Payroll</h3>
            <form method="POST" action="{{ route('payroll.generate') }}" class="flex flex-wrap items-end gap-4">
                @csrf
                <div class="flex-1 min-w-[150px]">
                    <label for="month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month</label>
                    <input type="month" id="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" 
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition @error('month') border-red-500 @enderror"
                           aria-describedby="month-error">
                    @error('month')
                        <p id="month-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <button type="submit" 
                        class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 
                               dark:bg-green-500 dark:hover:bg-green-600 text-sm transition"
                        onclick="return confirm('Generate payroll for the selected month?');">
                    📊 Generate Monthly Payroll
                </button>
            </form>
        </div>
    </section>

   

    <!-- Payroll List -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Payroll Records</h3>
                <div class="flex space-x-4">
                    <a href="{{ route('payroll.export') }}?start_date={{ request('start_date', $start) }}&end_date={{ request('end_date', $end) }}" 
                       class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 
                              dark:bg-gray-500 dark:hover:bg-gray-600 text-sm transition">
                        📥 Export PDF
                    </a>
                </div>
            </div>
            @if ($payrolls->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No payroll records found yet.</p>
                    <a href="{{ route('payroll.create') }}" 
                       class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Add Your First Payroll
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Employee</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Pay Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Base Salary (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bonus (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Deductions (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Net Pay (GHS)</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($payrolls as $payroll)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $payroll->employee ? $payroll->employee->name : 'N/A' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $payroll->pay_date ? \Carbon\Carbon::parse($payroll->pay_date)->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS {{ number_format($payroll->base_salary, 2) }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS {{ number_format($payroll->bonus, 2) }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">GHS {{ number_format($payroll->deductions, 2) }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300 font-medium">GHS {{ number_format($payroll->net_pay, 2) }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $payroll->status == 'paid' ? 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200' : 'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200' }}">
                                            {{ ucfirst($payroll->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 flex space-x-2">
                                        <a href="{{ route('payroll.show', $payroll) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-xs transition">
                                            👀 View
                                        </a>
                                        <a href="{{ route('payroll.edit', $payroll) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 text-xs transition">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to delete payroll for {{ $payroll->employee ? $payroll->employee->name : 'N/A' }} on {{ $payroll->pay_date ? \Carbon\Carbon::parse($payroll->pay_date)->format('Y-m-d') : 'N/A' }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 text-xs transition">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($payrolls instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-6 flex justify-end">
                        {{ $payrolls->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>


@endsection