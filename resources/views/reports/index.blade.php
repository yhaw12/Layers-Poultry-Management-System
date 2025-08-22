```php
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Reports</h2>
        </section>

        <!-- Filter Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('reports.index') }}" class="space-y-6">
                    <!-- Success/Error Messages -->
                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Report Type -->
                    <div>
                        <label for="type" class="block text-gray-700 dark:text-gray-300">Report Type <span class="text-red-600">*</span></label>
                        <select name="type" id="type" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('type') border-red-500 @enderror" required aria-describedby="type-error" onchange="toggleMetrics(this.value)">
                            <option value="weekly" {{ $reportType == 'weekly' ? 'selected' : '' }}>Weekly Egg Report</option>
                            <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Monthly Egg Report</option>
                            <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Custom Report</option>
                            <option value="profitability" {{ $reportType == 'profitability' ? 'selected' : '' }}>Profitability Report</option>
                            <option value="profit-loss" {{ $reportType == 'profit-loss' ? 'selected' : '' }}>Profit & Loss Report</option>
                            <option value="forecast" {{ $reportType == 'forecast' ? 'selected' : '' }}>Financial Forecast</option>
                        </select>
                        @error('type')
                            <p id="type-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Date Range -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:gap-6">
                        <div class="flex-1">
                            <label for="start_date" class="block text-gray-700 dark:text-gray-300">Start Date <span class="text-red-600">*</span></label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', isset($data['profit_loss']) ? $data['profit_loss']['start'] : now()->subMonths(6)->startOfMonth()->toDateString()) }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('start_date') border-red-500 @enderror"
                                   aria-describedby="start_date-error">
                            @error('start_date')
                                <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div class="flex-1">
                            <label for="end_date" class="block text-gray-700 dark:text-gray-300">End Date <span class="text-red-600">*</span></label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', isset($data['profit_loss']) ? $data['profit_loss']['end'] : now()->endOfMonth()->toDateString()) }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('end_date') border-red-500 @enderror"
                                   aria-describedby="end_date-error">
                            @error('end_date')
                                <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                    </div>

                    <!-- Metrics (for Custom Report) -->
                    <div id="metrics-section" class="{{ $reportType == 'custom' ? '' : 'hidden' }}">
                        <label class="block text-gray-700 dark:text-gray-300">Metrics <span class="text-red-600">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="eggs" {{ in_array('eggs', old('metrics', [])) ? 'checked' : '' }} class="mr-2 dark:bg-gray-800 dark:border-gray-600">
                                Eggs
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="sales" {{ in_array('sales', old('metrics', [])) ? 'checked' : '' }} class="mr-2 dark:bg-gray-800 dark:border-gray-600">
                                Sales
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="expenses" {{ in_array('expenses', old('metrics', [])) ? 'checked' : '' }} class="mr-2 dark:bg-gray-800 dark:border-gray-600">
                                Expenses
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="payrolls" {{ in_array('payrolls', old('metrics', [])) ? 'checked' : '' }} class="mr-2 dark:bg-gray-800 dark:border-gray-600">
                                Payrolls
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="metrics[]" value="transactions" {{ in_array('transactions', old('metrics', [])) ? 'checked' : '' }} class="mr-2 dark:bg-gray-800 dark:border-gray-600">
                                Transactions
                            </label>
                        </div>
                        @error('metrics')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Generate Report
                        </button>
                        <a href="{{ route('reports.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Reset
                        </a>
                        <a href="{{ route('reports.export') }}?type={{ $reportType }}&format=pdf&start_date={{ old('start_date', isset($data['profit_loss']) ? $data['profit_loss']['start'] : '') }}&end_date={{ old('end_date', isset($data['profit_loss']) ? $data['profit_loss']['end'] : '') }}&{{ http_build_query(['metrics' => old('metrics', [])]) }}"
                           class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                            Export PDF
                        </a>
                        <a href="{{ route('reports.export') }}?type={{ $reportType }}&format=excel&start_date={{ old('start_date', isset($data['profit_loss']) ? $data['profit_loss']['start'] : '') }}&end_date={{ old('end_date', isset($data['profit_loss']) ? $data['profit_loss']['end'] : '') }}&{{ http_build_query(['metrics' => old('metrics', [])]) }}"
                           class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                            Export Excel
                        </a>
                    </div>
                </form>
            </div>
        </section>

        <!-- Report Data -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                @if ($reportType == 'weekly')
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Weekly Egg Report</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Week</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($data['weekly'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->year }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->week }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($row->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif ($reportType == 'monthly')
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Monthly Egg Report</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($data['monthly'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->year }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($row->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif ($reportType == 'custom')
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Custom Report</h3>
                    @if (isset($data['eggs']) && $data['eggs']->count() > 0)
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Eggs</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Laid</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Crates</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($data['eggs'] as $egg)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $egg->date_laid }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($egg->crates, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (isset($data['sales']) && $data['sales']->count() > 0)
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Sales</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($data['sales'] as $sale)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->sale_date }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->customer->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">
                                                {{ $sale->saleable_type == 'App\Models\Bird' ? ($sale->saleable->breed ?? 'N/A') : 'Egg Batch ' . ($sale->saleable_id ?? 'N/A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($sale->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (isset($data['expenses']) && $data['expenses']->count() > 0)
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Expenses</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($data['expenses'] as $expense)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $expense->date }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $expense->description }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($expense->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (isset($data['payrolls']) && $data['payrolls']->count() > 0)
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Payrolls</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pay Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Pay</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($data['payrolls'] as $payroll)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $payroll->pay_date }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $payroll->employee->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($payroll->net_pay, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($payroll->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (isset($data['transactions']) && $data['transactions']->count() > 0)
                        <h4 class="text-md font-semibold text-gray-800 dark:text-white mb-2">Transactions</h4>
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($data['transactions'] as $transaction)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $transaction->date }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($transaction->type) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($transaction->amount, 2) }}</td>
                                            <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $transaction->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (empty($data['eggs']) && empty($data['sales']) && empty($data['expenses']) && empty($data['payrolls']) && empty($data['transactions']))
                        <p class="text-gray-500 dark:text-gray-400">No data available for the selected metrics.</p>
                    @endif
                @elseif ($reportType == 'profitability')
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Profitability Report</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bird ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Breed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Feed Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operational Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($data['profitability'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->bird_id ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->breed }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($row->type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($row->sales, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($row->feed_cost, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($row->operational_cost ?? ($row->total_expenses + $row->total_payroll), 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($row->profit, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No data available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @elseif ($reportType == 'profit-loss')
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Profit & Loss Report</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metric</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Total Income</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['profit_loss']['total_income'], 2) }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Total Expenses</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['profit_loss']['total_expenses'], 2) }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Total Payroll</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['profit_loss']['total_payroll'], 2) }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Profit/Loss</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['profit_loss']['profit_loss'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">Period: {{ $data['profit_loss']['start'] }} to {{ $data['profit_loss']['end'] }}</p>
                @elseif ($reportType == 'forecast')
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Financial Forecast</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metric</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1a1a3a] divide-y divide-gray-200 dark:divide-gray-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Forecasted Income</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['forecast']['forecasted_income'], 2) }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Forecasted Expenses</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['forecast']['forecasted_expenses'], 2) }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">Forecasted Profit</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">${{ number_format($data['forecast']['forecasted_profit'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-gray-500 dark:text-gray-400">Based on past 6 months data with 5% income growth and 3% expense growth.</p>
                @endif
            </div>
        </section>
    </div>

@push('scripts')
    <script>
        function toggleMetrics(reportType) {
            const metricsSection = document.getElementById('metrics-section');
            metricsSection.classList.toggle('hidden', reportType !== 'custom');
        }
    </script>
@endpush
@endsection
```
{{-- @extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header with Export Button -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Farm Reports</h1>
            <div class="relative">
                <button id="export-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Export Report
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                    <button data-format="pdf" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">PDF</button>
                    <button data-format="excel" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Excel</button>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8">
            <nav class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700" role="tablist">
                @foreach (['weekly', 'monthly', 'custom', 'profitability', 'profit-loss', 'forecast'] as $tab)
                    <button data-tab="{{ $tab }}"
                            class="tab-btn px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 {{ $reportType === $tab ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                            role="tab" aria-selected="{{ $reportType === $tab ? 'true' : 'false' }}"
                            aria-controls="{{ $tab }}-panel">
                            {{ $tab === 'custom' ? 'Analytics' : ucfirst(str_replace('-', ' ', $tab)) }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Report Panels -->
        <div id="report-content" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <!-- Weekly Report -->
            <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Weekly Egg Report</h2>
                @if (empty($data['weekly']) || $data['weekly']->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No egg data found for the last 8 weeks.</p>
                @else
                    <div class="mb-6 h-64">
                        <canvas id="weekly-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('weekly-chart'), {
                                type: 'line',
                                data: {
                                    labels: @json($data['weekly']->map(fn($row) => "Week {$row->week}, {$row->year}")),
                                    datasets: [{
                                        label: 'Total Eggs',
                                        data: @json($data['weekly']->pluck('total')),
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: { display: true, text: 'Eggs' }
                                        },
                                        x: {
                                            title: { display: true, text: 'Week' }
                                        }
                                    },
                                    plugins: {
                                        legend: { position: 'top' }
                                    }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Week</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Eggs</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['weekly'] as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->week }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Monthly Report -->
            <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Monthly Egg Report</h2>
                @if (empty($data['monthly']) || $data['monthly']->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No egg data found for the last 6 months.</p>
                @else
                    <div class="mb-6 h-64">
                        <canvas id="monthly-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('monthly-chart'), {
                                type: 'bar',
                                data: {
                                    labels: @json($data['monthly']->map(fn($row) => \Carbon\Carbon::create()->month($row->month_num)->format('F') . " {$row->year}")),
                                    datasets: [{
                                        label: 'Total Crates',
                                        data: @json($data['monthly']->pluck('total')),
                                        backgroundColor: '#8b5cf6',
                                        borderColor: '#7c3aed',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: { display: true, text: 'Crates' }
                                        },
                                        x: {
                                            title: { display: true, text: 'Month' }
                                        }
                                    },
                                    plugins: {
                                        legend: { position: 'top' }
                                    }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['monthly'] as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Analytics (Custom) Report -->
            <div id="custom-panel" class="tab-panel {{ $reportType === 'custom' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Analytics Report</h2>
                <form id="custom-report-form" method="GET" action="{{ route('reports.custom') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="custom">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                        <input type="date" id="end_date" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metrics</label>
                        <div class="mt-1 space-y-2">
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="eggs" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Eggs</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="sales" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Sales</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="expenses" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Expenses</span>
                                </label>
                            </div>
                        </div>
                        @error('metrics')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50" id="submit-btn">
                            <span>Generate Report</span>
                            <svg id="loading-spinner" class="hidden w-5 h-5 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                @if (!empty($data['eggs']) || !empty($data['sales']) || !empty($data['expenses']))
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Analytics Report Results</h3>
                        @if (!empty($data['eggs']))
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Eggs</h4>
                                <div class="h-64">
                                    <canvas id="eggs-chart"></canvas>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        new Chart(document.getElementById('eggs-chart'), {
                                            type: 'line',
                                            data: {
                                                labels: @json($data['eggs']->pluck('date_laid')),
                                                datasets: [{
                                                    label: 'Egg Quantity',
                                                    data: @json($data['eggs']->pluck('quantity')->map(fn($q) => $q ?? 1)),
                                                    borderColor: '#f59e0b',
                                                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                                                    fill: true
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                scales: {
                                                    y: { beginAtZero: true, title: { display: true, text: 'Quantity' } },
                                                    x: { title: { display: true, text: 'Date' } }
                                                },
                                                plugins: { legend: { position: 'top' } }
                                            }
                                        });
                                    });
                                </script>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Laid</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($data['eggs'] as $egg)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $egg->date_laid }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $egg->quantity ?? 1 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if (!empty($data['sales']))
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Sales</h4>
                                <div class="h-64">
                                    <canvas id="sales-chart"></canvas>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        new Chart(document.getElementById('sales-chart'), {
                                            type: 'bar',
                                            data: {
                                                labels: @json($data['sales']->pluck('sale_date')),
                                                datasets: [{
                                                    label: 'Total Sales ($)',
                                                    data: @json($data['sales']->pluck('total_amount')),
                                                    backgroundColor: '#ef4444',
                                                    borderColor: '#dc2626',
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                scales: {
                                                    y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                                                    x: { title: { display: true, text: 'Date' } }
                                                },
                                                plugins: { legend: { position: 'top' } }
                                            }
                                        });
                                    });
                                </script>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($data['sales'] as $sale)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->sale_date }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->customer->name ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->quantity }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($sale->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if (!empty($data['expenses']))
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Expenses</h4>
                                <div class="h-64">
                                    <canvas id="expenses-chart"></canvas>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        new Chart(document.getElementById('expenses-chart'), {
                                            type: 'bar',
                                            data: {
                                                labels: @json($data['expenses']->pluck('date')),
                                                datasets: [{
                                                    label: 'Total Expenses ($)',
                                                    data: @json($data['expenses']->pluck('amount')),
                                                    backgroundColor: '#f97316',
                                                    borderColor: '#ea580c',
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                scales: {
                                                    y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                                                    x: { title: { display: true, text: 'Date' } }
                                                },
                                                plugins: { legend: { position: 'top' } }
                                            }
                                        });
                                    });
                                </script>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($data['expenses'] as $expense)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $expense->date }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $expense->description ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($expense->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Profitability Report -->
            <div id="profitability-panel" class="tab-panel {{ $reportType === 'profitability' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Profitability Report</h2>
                @if (empty($data['profitability']) || $data['profitability']->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No profitability data found.</p>
                @else
                    <div class="mb-6 h-64">
                        <canvas id="profitability-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('profitability-chart'), {
                                type: 'bar',
                                data: {
                                    labels: @json($data['profitability']->pluck('breed')),
                                    datasets: [
                                        {
                                            label: 'Sales ($)',
                                            data: @json($data['profitability']->pluck('sales')),
                                            backgroundColor: '#10b981'
                                        },
                                        {
                                            label: 'Feed Cost ($)',
                                            data: @json($data['profitability']->pluck('feed_cost')),
                                            backgroundColor: '#f97316'
                                        },
                                        {
                                            label: 'Expenses ($)',
                                            data: @json($data['profitability']->pluck('expenses')),
                                            backgroundColor: '#ef4444'
                                        },
                                        {
                                            label: 'Profit ($)',
                                            data: @json($data['profitability']->pluck('profit')),
                                            backgroundColor: '#3b82f6'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                                        x: { title: { display: true, text: 'Breed' } }
                                    },
                                    plugins: { legend: { position: 'top' } }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bird ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Breed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales ($)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Feed Cost ($)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expenses ($)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profit ($)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['profitability'] as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->bird_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->breed }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->sales, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->feed_cost, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->expenses, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap {{ $row->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($row->profit, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Profit and Loss Report -->
            @role('admin')
            <div id="profit-loss-panel" class="tab-panel {{ $reportType === 'profit-loss' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Profit and Loss Report</h2>
                <form method="GET" action="{{ route('reports.index', ['type' => 'profit-loss']) }}" class="mb-6">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $data['profit_loss']['start'] ?? now()->startOfMonth()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                        <div class="flex-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $data['profit_loss']['end'] ?? now()->endOfMonth()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Filter</button>
                    </div>
                </form>
                @if (!empty($data['profit_loss']))
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Income</h3>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($data['profit_loss']['total_income'], 2) }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Total Expenses</h3>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($data['profit_loss']['total_expenses'], 2) }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profit/Loss</h3>
                            <p class="text-2xl font-bold {{ $data['profit_loss']['profit_loss'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($data['profit_loss']['profit_loss'], 2) }}
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No data found for the selected period.</p>
                @endif
            </div>
            @endrole

            <!-- Forecast Report -->
            @role('admin')
            <div id="forecast-panel" class="tab-panel {{ $reportType === 'forecast' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Financial Forecast</h2>
                @if (!empty($data['forecast']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Forecasted Income</h3>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($data['forecast']['forecasted_income'], 2) }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Forecasted Expenses</h3>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($data['forecast']['forecasted_expenses'], 2) }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No forecast data available.</p>
                @endif
            </div>
            @endrole
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tab-btn');
            const exportBtn = document.getElementById('export-btn');
            const exportDropdown = document.getElementById('export-dropdown');
            const exportOptions = exportDropdown.querySelectorAll('button');

            // Tab switching with page reload
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabType = tab.getAttribute('data-tab');
                    const url = new URL(window.location);
                    url.searchParams.set('type', tabType);
                    window.location.href = url.toString(); // Reload page with new type
                });
            });

            // Export Dropdown
            exportBtn.addEventListener('click', () => {
                exportDropdown.classList.toggle('hidden');
            });

            exportOptions.forEach(option => {
                option.addEventListener('click', (e) => {
                    e.preventDefault();
                    const format = option.getAttribute('data-format');
                    const currentType = new URLSearchParams(window.location.search).get('type') || 'weekly';
                    let url = `{{ route('reports.export') }}?type=${currentType}&format=${format}`;
                    
                    if (currentType === 'custom') {
                        const form = document.getElementById('custom-report-form');
                        if (form.checkValidity()) {
                            const formData = new FormData(form);
                            formData.set('format', format);
                            formData.set('type', 'custom');
                            url = '{{ route('reports.export') }}?' + new URLSearchParams(formData).toString();
                            window.location.href = url;
                        } else {
                            alert('Please fill all required fields in the analytics report form.');
                            exportDropdown.classList.add('hidden');
                        }
                    } else {
                        window.location.href = url;
                    }
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!exportBtn.contains(e.target) && !exportDropdown.contains(e.target)) {
                    exportDropdown.classList.add('hidden');
                }
            });

            // Custom Report Form Submission
            const form = document.getElementById('custom-report-form');
            const submitBtn = document.getElementById('submit-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            if (form && submitBtn && loadingSpinner) {
                form.addEventListener('submit', (e) => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        alert('Please fill all required fields.');
                        return;
                    }
                    submitBtn.disabled = true;
                    loadingSpinner.classList.remove('hidden');
                    submitBtn.querySelector('span').textContent = 'Generating...';
                    // Fallback timeout
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.disabled = false;
                            loadingSpinner.classList.add('hidden');
                            submitBtn.querySelector('span').textContent = 'Generate Report';
                            alert('Report generation timed out. Please try again.');
                        }
                    }, 10000);
                });
            }
        });
    </script>
@endsection --}}

