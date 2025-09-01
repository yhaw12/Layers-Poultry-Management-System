@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-50 dark:bg-gray-900">
    <!-- Toast Notification -->
    <div id="toast" class="fixed right-6 top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4">
        <div id="toastInner" class="max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 text-white flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span></span>
        </div>
    </div>

    <!-- Header -->
    <section class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Advanced Farm Analytics</h2>
        <div class="flex space-x-4">
            <div class="relative">
                <button id="export-btn" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-blue-600 dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Report
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-xl z-10 border border-gray-200 dark:border-gray-700">
                    <button data-format="pdf" class="export-option w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">PDF</button>
                    <button data-format="excel" class="export-option w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">Excel</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Form -->
    <section>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
            <form id="report-filter-form" method="GET" action="{{ route('reports.index') }}" class="space-y-6">
                @csrf
                @if (session('error'))
                    <div class="p-4 bg-red-100 dark:bg-red-900/80 text-red-800 dark:text-red-200 rounded-xl flex items-center space-x-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="p-4 bg-red-100 dark:bg-red-900/80 text-red-800 dark:text-red-200 rounded-xl">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tabs -->
                <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
                    @foreach (['weekly', 'monthly', 'custom', 'profitability', 'profit-loss', 'forecast'] as $tab)
                        <button type="button" data-tab="{{ $tab }}"
                                class="tab-btn px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 {{ $reportType === $tab ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                role="tab" aria-selected="{{ $reportType === $tab ? 'true' : 'false' }}"
                                aria-controls="{{ $tab }}-panel">
                            {{ $tab === 'custom' ? 'Analytics' : ucfirst(str_replace('-', ' ', $tab)) }}
                        </button>
                    @endforeach
                    <input type="hidden" name="type" id="report-type" value="{{ $reportType }}">
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date <span class="text-red-600">*</span></label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', (!empty($data['profit_loss']) ? $data['profit_loss']['start'] : now()->subMonths(6)->startOfMonth()->toDateString())) }}"
                               class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                        @error('start_date')
                            <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date <span class="text-red-600">*</span></label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', (!empty($data['profit_loss']) ? $data['profit_loss']['end'] : now()->endOfMonth()->toDateString())) }}"
                               class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                        @error('end_date')
                            <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div id="metrics-section" class="{{ $reportType == 'custom' ? '' : 'hidden' }} flex-1 min-w-[180px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metrics <span class="text-red-600">*</span></label>
                        <div class="space-y-2 mt-2">
                            @foreach (['eggs', 'sales', 'expenses', 'payrolls', 'transactions'] as $metric)
                                <label class="flex items-center">
                                    <input type="checkbox" name="metrics[]" value="{{ $metric }}" {{ in_array($metric, old('metrics', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">{{ ucfirst($metric) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('metrics')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" id="submit-btn" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-blue-600 dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium">
                        <svg id="submit-spinner" class="hidden animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <span>Generate Report</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    @php
        // Normalize server data so template code never accesses missing $data['...'] keys
        $weekly = $data['weekly'] ?? collect();
        $monthly = $data['monthly'] ?? collect();
        $eggs = $data['eggs'] ?? collect();
        $sales = $data['sales'] ?? collect();
        $expenses = $data['expenses'] ?? collect();
        $payrolls = $data['payrolls'] ?? collect();
        $transactions = $data['transactions'] ?? collect();
        $profitability = $data['profitability'] ?? collect();
        $profit_loss = $data['profit_loss'] ?? [];
        $forecast = $data['forecast'] ?? [];
    @endphp

    <!-- Report Panels -->
    <section class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
        <!-- Weekly Report -->
        <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Weekly Egg Report</h3>
            @if ($weekly->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No egg data found for the selected period.</p>
            @else
                <div class="mb-8 h-80">
                    <canvas id="weekly-chart"></canvas>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Week</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($weekly as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->week }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Check if $weekly is paginated before rendering links -->
                @if ($weekly instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $weekly->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Monthly Report -->
        <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Monthly Egg Report</h3>
            @if ($monthly->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No egg data found for the selected period.</p>
            @else
                <div class="mb-8 h-80">
                    <canvas id="monthly-chart"></canvas>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($monthly as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Check if $monthly is paginated before rendering links -->
                @if ($monthly instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $monthly->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Custom Report -->
        <div id="custom-panel" class="tab-panel {{ $reportType === 'custom' ? '' : 'hidden' }}">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Analytics Report</h3>
            @if ($eggs->isNotEmpty() || $sales->isNotEmpty() || $expenses->isNotEmpty() || $payrolls->isNotEmpty() || $transactions->isNotEmpty())
                @if ($eggs->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Eggs</h4>
                        <div class="h-80">
                            <canvas id="eggs-chart"></canvas>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Laid</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Crates</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($eggs as $egg)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $egg->date_laid }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($egg->crates, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Check if $eggs is paginated before rendering links -->
                        @if ($eggs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $eggs->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if ($sales->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Sales</h4>
                        <div class="h-80">
                            <canvas id="sales-chart"></canvas>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total (₵)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($sales as $sale)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->sale_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->customer->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">
                                            {{ $sale->saleable_type == 'App\Models\Bird' ? ($sale->saleable->breed ?? 'N/A') : 'Egg Batch ' . ($sale->saleable_id ?? 'N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Check if $sales is paginated before rendering links -->
                        @if ($sales instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $sales->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if ($expenses->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Expenses</h4>
                        <div class="h-80">
                            <canvas id="expenses-chart"></canvas>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount (₵)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($expenses as $expense)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $expense->date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $expense->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Check if $expenses is paginated before rendering links -->
                        @if ($expenses instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $expenses->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if ($payrolls->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payrolls</h4>
                        <div class="h-80">
                            <canvas id="payrolls-chart"></canvas>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pay Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Pay (₵)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($payrolls as $payroll)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $payroll->pay_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $payroll->employee->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($payroll->net_pay, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($payroll->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Check if $payrolls is paginated before rendering links -->
                        @if ($payrolls instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $payrolls->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    </div>
                @endif

                @if ($transactions->isNotEmpty())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Transactions</h4>
                        <div class="h-80">
                            <canvas id="transactions-chart"></canvas>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                            <thead class="bg-gray-50 dark:bg-gray-700/80">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount (₵)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $transaction->date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($transaction->type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($transaction->amount, 2) }}</td>
                                        <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $transaction->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Check if $transactions is paginated before rendering links -->
                        @if ($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $transactions->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No data available for the selected metrics.</p>
            @endif
        </div>

        <!-- Profitability Report -->
        <div id="profitability-panel" class="tab-panel {{ $reportType === 'profitability' ? '' : 'hidden' }}">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Profitability Report</h3>
            @if ($profitability->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No profitability data found.</p>
            @else
                <div class="mb-8 h-80">
                    <canvas id="profitability-chart"></canvas>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bird ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Breed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales (₵)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Feed Cost (₵)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operational Cost (₵)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profit (₵)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($profitability as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->bird_id ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->breed }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($row->type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($row->sales, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($row->feed_cost, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($row->operational_cost ?? ($row->total_expenses + $row->total_payroll), 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap {{ $row->profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    ₵ {{ number_format($row->profit, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Check if $profitability is paginated before rendering links -->
                @if ($profitability instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $profitability->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Profit and Loss Report -->
        @role('admin')
        <div id="profit-loss-panel" class="tab-panel {{ $reportType === 'profit-loss' ? '' : 'hidden' }}">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Profit & Loss Report</h3>
            @if (!empty($profit_loss))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg transform transition-all hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Total Income</h4>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">₵ {{ number_format($profit_loss['total_income'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg transform transition-all hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Total Expenses</h4>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">₵ {{ number_format(($profit_loss['total_expenses'] ?? 0) + ($profit_loss['total_payroll'] ?? 0), 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg transform transition-all hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Profit/Loss</h4>
                        <p class="text-2xl font-bold {{ ($profit_loss['profit_loss'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ₵ {{ number_format($profit_loss['profit_loss'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="profit-loss-chart"></canvas>
                </div>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Period: {{ $profit_loss['start'] ?? '' }} to {{ $profit_loss['end'] ?? '' }}</p>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No data found for the selected period.</p>
            @endif
        </div>
        @endrole

        <!-- Forecast Report -->
        @role('admin')
        <div id="forecast-panel" class="tab-panel {{ $reportType === 'forecast' ? '' : 'hidden' }}">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Financial Forecast</h3>
            @if (!empty($forecast))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg transform transition-all hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Forecasted Income</h4>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">₵ {{ number_format($forecast['forecasted_income'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg transform transition-all hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Forecasted Expenses</h4>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">₵ {{ number_format($forecast['forecasted_expenses'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg transform transition-all hover:scale-105">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Forecasted Profit</h4>
                        <p class="text-2xl font-bold {{ ($forecast['forecasted_profit'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ₵ {{ number_format($forecast['forecasted_profit'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="forecast-chart"></canvas>
                </div>
                <p class="mt-4 text-gray-600 dark:text-gray-400">Based on past 6 months data with 5% income growth and 3% expense growth.</p>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">No forecast data available.</p>
            @endif
        </div>
        @endrole
    </section>
</div>

@push('scripts')
<!-- Chart.js UMD build (works with <script> usage) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
    // ----- Helpers -----
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let charts = {};

    function showToast(message, timeout = 3000) {
        const toast = document.getElementById('toast');
        const innerSpan = document.querySelector('#toastInner span');
        if (innerSpan) innerSpan.innerText = message;
        toast.classList.remove('invisible', 'opacity-0', 'translate-y-4');
        toast.classList.add('opacity-100', 'pointer-events-auto');
        setTimeout(() => {
            toast.classList.add('opacity-0');
            toast.classList.remove('pointer-events-auto');
            setTimeout(() => toast.classList.add('invisible'), 300);
        }, timeout);
    }

    function formatCurrency(num) {
        return Number(num || 0).toFixed(2);
    }

    // ----- Chart Initialization -----
    function initCharts() {
        // Use server-provided, normalized collections (fallback to [])
        const chartConfigs = {
            'weekly': {
                element: 'weekly-chart',
                type: 'line',
                data: {
                    labels: @json($weekly->map(fn($row) => "Week {$row->week}, {$row->year}")),
                    datasets: [{
                        label: 'Total Crates',
                        data: @json($weekly->pluck('total')),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                }
            },
            'monthly': {
                element: 'monthly-chart',
                type: 'bar',
                data: {
                    labels: @json($monthly->map(fn($row) => \Carbon\Carbon::create()->month($row->month_num)->format('F Y'))),
                    datasets: [{
                        label: 'Total Crates',
                        data: @json($monthly->pluck('total')),
                        backgroundColor: '#8b5cf6',
                        borderColor: '#7c3aed',
                        borderWidth: 1
                    }]
                }
            },
            'eggs': {
                element: 'eggs-chart',
                type: 'line',
                data: {
                    labels: @json($eggs->pluck('date_laid')),
                    datasets: [{
                        label: 'Crates',
                        data: @json($eggs->pluck('crates')),
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                }
            },
            'sales': {
                element: 'sales-chart',
                type: 'bar',
                data: {
                    labels: @json($sales->pluck('sale_date')),
                    datasets: [
                        {
                            label: 'Total Amount (₵)',
                            data: @json($sales->pluck('total_amount')),
                            backgroundColor: '#ef4444',
                            borderColor: '#dc2626',
                            borderWidth: 1
                        },
                        {
                            label: 'Quantity',
                            data: @json($sales->pluck('quantity')),
                            backgroundColor: '#3b82f6',
                            borderColor: '#2563eb',
                            borderWidth: 1
                        }
                    ]
                }
            },
            'expenses': {
                element: 'expenses-chart',
                type: 'bar',
                data: {
                    labels: @json($expenses->pluck('date')),
                    datasets: [{
                        label: 'Amount (₵)',
                        data: @json($expenses->pluck('amount')),
                        backgroundColor: '#f97316',
                        borderColor: '#ea580c',
                        borderWidth: 1
                    }]
                }
            },
            'payrolls': {
                element: 'payrolls-chart',
                type: 'bar',
                data: {
                    labels: @json($payrolls->pluck('pay_date')),
                    datasets: [
                        {
                            label: 'Net Pay (₵)',
                            data: @json($payrolls->pluck('net_pay')),
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1
                        },
                        {
                            label: 'Bonus (₵)',
                            data: @json($payrolls->pluck('bonus')),
                            backgroundColor: '#f59e0b',
                            borderColor: '#d97706',
                            borderWidth: 1
                        }
                    ]
                }
            },
            'transactions': {
                element: 'transactions-chart',
                type: 'line',
                data: {
                    labels: @json($transactions->pluck('date')),
                    datasets: [
                        {
                            label: 'Amount (₵)',
                            data: @json($transactions->pluck('amount')),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                }
            },
            'profitability': {
                element: 'profitability-chart',
                type: 'bar',
                data: {
                    labels: @json($profitability->pluck('breed')),
                    datasets: [
                        {
                            label: 'Sales (₵)',
                            data: @json($profitability->pluck('sales')),
                            backgroundColor: '#10b981'
                        },
                        {
                            label: 'Feed Cost (₵)',
                            data: @json($profitability->pluck('feed_cost')),
                            backgroundColor: '#f97316'
                        },
                        {
                            label: 'Operational Cost (₵)',
                            data: @json($profitability->map(fn($row) => $row->operational_cost ?? ($row->total_expenses + $row->total_payroll))),
                            backgroundColor: '#ef4444'
                        },
                        {
                            label: 'Profit (₵)',
                            data: @json($profitability->pluck('profit')),
                            backgroundColor: '#3b82f6'
                        }
                    ]
                }
            },
            'profit-loss': {
                element: 'profit-loss-chart',
                type: 'bar',
                data: {
                    labels: ['Income', 'Expenses', 'Profit/Loss'],
                    datasets: [{
                        label: 'Amount (₵)',
                        data: [
                            @json($profit_loss['total_income'] ?? 0),
                            @json(($profit_loss['total_expenses'] ?? 0) + ($profit_loss['total_payroll'] ?? 0)),
                            @json($profit_loss['profit_loss'] ?? 0)
                        ],
                        backgroundColor: [
                            @json('#10b981'),
                            @json('#ef4444'),
                            @json((($profit_loss['profit_loss'] ?? 0) >= 0) ? '#3b82f6' : '#dc2626')
                        ]
                    }]
                }
            },
            'forecast': {
                element: 'forecast-chart',
                type: 'bar',
                data: {
                    labels: ['Income', 'Expenses', 'Profit'],
                    datasets: [{
                        label: 'Amount (₵)',
                        data: [
                            @json($forecast['forecasted_income'] ?? 0),
                            @json($forecast['forecasted_expenses'] ?? 0),
                            @json($forecast['forecasted_profit'] ?? 0)
                        ],
                        backgroundColor: [
                            @json('#10b981'),
                            @json('#ef4444'),
                            @json((($forecast['forecasted_profit'] ?? 0) >= 0) ? '#3b82f6' : '#dc2626')
                        ]
                    }]
                }
            }
        };

        Object.entries(chartConfigs).forEach(([key, config]) => {
            try {
                const canvas = document.getElementById(config.element);
                const labels = (config.data && config.data.labels) || [];
                const hasData = Array.isArray(labels) && labels.length > 0;
                if (canvas && hasData) {
                    if (charts[key]) {
                        try { charts[key].destroy(); } catch (err) { /* ignore */ }
                    }
                    charts[key] = new Chart(canvas, {
                        type: config.type,
                        data: config.data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Amount (₵)' },
                                    grid: { color: '#e5e7eb', borderColor: '#d1d5db' }
                                },
                                x: {
                                    title: { display: true, text: key === 'profitability' ? 'Breed' : 'Date' },
                                    grid: { display: false }
                                }
                            },
                            plugins: {
                                legend: { position: 'top' },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => {
                                            const value = context.raw ?? context.parsed?.y ?? 0;
                                            return `${context.dataset.label}: ₵ ${formatCurrency(value)}`;
                                        }
                                    }
                                }
                            },
                            animation: { duration: 600, easing: 'easeOutQuart' }
                        }
                    });
                }
            } catch (err) {
                console.warn('Chart init failed for', key, err);
            }
        });
    }

    // ----- Tab Switching -----
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-blue-500', 'text-white');
            btn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            btn.setAttribute('aria-selected', 'false');
        });

        const panel = document.getElementById(`${tab}-panel`);
        const btn = document.querySelector(`[data-tab="${tab}"]`);
        if (panel && btn) {
            panel.classList.remove('hidden');
            btn.classList.add('bg-blue-500', 'text-white');
            btn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            btn.setAttribute('aria-selected', 'true');
            document.getElementById('report-type').value = tab;
            document.getElementById('metrics-section').classList.toggle('hidden', tab !== 'custom');
        }
    }

    // ----- Form helpers -----
    function buildQueryFromForm(form) {
        const params = new URLSearchParams();
        const fd = new FormData(form);
        for (const [k, v] of fd.entries()) {
            if (k === 'metrics' || k === 'metrics[]') {
                params.append('metrics[]', v);
            } else {
                params.append(k, v);
            }
        }
        return params.toString();
    }

    function showGeneratingState(show = true) {
        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('submit-spinner');
        if (show) {
            submitBtn.setAttribute('disabled', 'true');
            spinner.classList.remove('hidden');
            submitBtn.querySelector('span').textContent = 'Generating...';
        } else {
            submitBtn.removeAttribute('disabled');
            spinner.classList.add('hidden');
            submitBtn.querySelector('span').textContent = 'Generate Report';
        }
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const qs = buildQueryFromForm(form);
        const url = form.action + (qs ? ('?' + qs) : '');
        showGeneratingState(true);
        window.location.href = url;
    }

    // ----- Export Handling -----
    function handleExport(format) {
        const form = document.getElementById('report-filter-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const qs = buildQueryFromForm(form);
        const url = '{{ route('reports.export') }}' + (qs ? ('?' + qs + '&') : '?') + 'format=' + encodeURIComponent(format) + '&type=' + encodeURIComponent(document.getElementById('report-type').value || '');
        showToast(`Exporting ${format.toUpperCase()}...`, 2000);
        window.location.href = url;
    }

    // ----- Event Listeners -----
    document.addEventListener('DOMContentLoaded', () => {
        try { initCharts(); } catch (err) { console.warn('initCharts failed', err); }

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.getAttribute('data-tab');
                switchTab(tab);
                const form = document.getElementById('report-filter-form');
                document.getElementById('report-type').value = tab;
                const qs = buildQueryFromForm(form);
                const url = form.action + (qs ? ('?' + qs) : '');
                showGeneratingState(true);
                window.location.href = url;
            });
        });

        const exportBtn = document.getElementById('export-btn');
        const exportDropdown = document.getElementById('export-dropdown');
        if (exportBtn && exportDropdown) {
            exportBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                exportDropdown.classList.toggle('hidden');
            });

            document.querySelectorAll('.export-option').forEach(option => {
                option.addEventListener('click', (ev) => {
                    ev.preventDefault();
                    const format = option.getAttribute('data-format');
                    exportDropdown.classList.add('hidden');
                    handleExport(format);
                });
            });

            document.addEventListener('click', (ev) => {
                if (!exportBtn.contains(ev.target) && !exportDropdown.contains(ev.target)) {
                    exportDropdown.classList.add('hidden');
                }
            });
        }

        const form = document.getElementById('report-filter-form');
        if (form) form.addEventListener('submit', handleFormSubmit);

        switchTab('{{ $reportType }}');
    });
</script>
@endpush
@endsection