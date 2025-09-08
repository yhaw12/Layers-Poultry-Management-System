{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12 space-y-16 bg-gray-50 dark:bg-gray-900">
    <!-- Toast Notification -->
    <div id="toast" class="fixed right-6 top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4">
        <div id="toastInner" class="max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 text-white flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span></span>
        </div>
    </div>

    <!-- Header + KPI cards + Quick Actions -->
    <section class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
        <div class="flex-1">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Advanced Farm Analytics</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-2">Generate, compare and export detailed reports for your farm operations.</p>

            <!-- Quick Presets -->
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <button type="button" class="preset-btn inline-flex items-center px-4 py-2 rounded-full text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-shadow duration-200" data-days="7">7d</button>
                <button type="button" class="preset-btn inline-flex items-center px-4 py-2 rounded-full text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-shadow duration-200" data-days="30">30d</button>
                <button type="button" class="preset-btn inline-flex items-center px-4 py-2 rounded-full text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-shadow duration-200" data-days="180">6M</button>
                <button type="button" class="preset-btn inline-flex items-center px-4 py-2 rounded-full text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-shadow duration-200" data-days="365">YTD</button>

                <label class="ml-6 flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <input id="compare-toggle" type="checkbox" class="ml-2 mr-2 rounded focus:ring-blue-500" /> Compare previous period
                </label>

                <label class="ml-4 flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <input id="cumulative-toggle" type="checkbox" class="ml-2 mr-2 rounded focus:ring-blue-500" /> Cumulative
                </label>
            </div>
        </div>

        <div class="flex-shrink-0 flex space-x-4 items-center">
            <div class="relative">
                <button id="export-btn" class="inline-flex items-center bg-blue-500 text-white px-5 py-3 rounded-xl shadow-md hover:bg-blue-600 dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600" aria-haspopup="menu" aria-expanded="false">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl z-10 border border-gray-200 dark:border-gray-700" role="menu" aria-label="Export options">
                    <button data-format="pdf" class="export-option w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">PDF</button>
                    <button data-format="excel" class="export-option w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">Excel</button>
                    <button data-format="csv" class="export-option w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">CSV</button>
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                    <button id="export-advanced" class="w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">Advanced export...</button>
                </div>
            </div>

            <button id="download-csv" class="inline-flex items-center bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-xl shadow-sm hover:shadow-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-shadow duration-200" title="Download visible table as CSV">
                CSV
            </button>
        </div>
    </section>

    <!-- KPI Cards (Advanced Metrics) with shimmer skeleton support -->
    <section id="kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $profit_loss = $data['profit_loss'] ?? [];
            $totals = $data['totals'] ?? [];
        @endphp

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md relative overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <div class="text-sm text-gray-500 dark:text-gray-300">Total Income</div>
            <div id="kpi-income" class="text-3xl font-bold mt-2">₵ {{ number_format($profit_loss['total_income'] ?? ($totals['income'] ?? 0), 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">Period: <span id="kpi-period">{{ $profit_loss['start'] ?? '' }} — {{ $profit_loss['end'] ?? '' }}</span></div>
            <div class="kpi-skel absolute inset-0 pointer-events-none opacity-0"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md relative overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <div class="text-sm text-gray-500 dark:text-gray-300">Total Expenses</div>
            <div id="kpi-expenses" class="text-3xl font-bold mt-2">₵ {{ number_format(($profit_loss['total_expenses'] ?? ($totals['expenses'] ?? 0)) + ($profit_loss['total_payroll'] ?? ($totals['payroll'] ?? 0)), 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">Includes payroll</div>
            <div class="kpi-skel absolute inset-0 pointer-events-none opacity-0"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md relative overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <div class="text-sm text-gray-500 dark:text-gray-300">Net Profit</div>
            <div id="kpi-profit" class="text-3xl font-bold mt-2 {{ (($profit_loss['profit_loss'] ?? 0) >= 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">₵ {{ number_format($profit_loss['profit_loss'] ?? ($totals['profit'] ?? 0), 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">After expenses</div>
            <div class="kpi-skel absolute inset-0 pointer-events-none opacity-0"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md relative overflow-hidden hover:shadow-lg transition-shadow duration-200">
            <div class="text-sm text-gray-500 dark:text-gray-300">Avg Crates / Day</div>
            <div id="kpi-avg" class="text-3xl font-bold mt-2">{{ number_format($data['avg_crates_per_day'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">Useful for production tracking</div>
            <div class="kpi-skel absolute inset-0 pointer-events-none opacity-0"></div>
        </div>
    </section>

    <!-- Filter Form -->
    <section>
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50">
            <form id="report-filter-form" method="GET" action="{{ route('reports.index') }}" class="space-y-8">
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
                <div class="flex flex-wrap gap-3 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                    @foreach (['weekly', 'monthly', 'custom', 'profitability', 'profit-loss', 'forecast'] as $tab)
                        <button type="button" data-tab="{{ $tab }}"
                                class="tab-btn px-5 py-3 text-sm font-medium rounded-t-lg transition-colors duration-200 {{ $reportType === $tab ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                role="tab" aria-selected="{{ $reportType === $tab ? 'true' : 'false' }}"
                                aria-controls="{{ $tab }}-panel">
                            {{ $tab === 'custom' ? 'Analytics' : ucfirst(str_replace('-', ' ', $tab)) }}
                        </button>
                    @endforeach
                    <input type="hidden" name="type" id="report-type" value="{{ $reportType }}">
                    <input type="hidden" name="compare" id="compare-field" value="{{ request('compare', '0') }}">
                    <input type="hidden" name="cumulative" id="cumulative-field" value="{{ request('cumulative', '0') }}">
                </div>

                <!-- Filters row -->
                <div class="flex flex-wrap gap-6 items-end mt-6">
                    <div class="flex-1 min-w-[200px]">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date <span class="text-red-600">*</span></label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', (!empty($data['profit_loss']) ? $data['profit_loss']['start'] : now()->subMonths(6)->startOfMonth()->toDateString())) }}"
                               class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                        @error('start_date')
                            <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date <span class="text-red-600">*</span></label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', (!empty($data['profit_loss']) ? $data['profit_loss']['end'] : now()->endOfMonth()->toDateString())) }}"
                               class="w-full border rounded-lg p-3 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                        @error('end_date')
                            <p id="end_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="metrics-section" class="{{ $reportType == 'custom' ? '' : 'hidden' }} flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metrics <span class="text-red-600">*</span></label>
                        <div class="space-y-3 mt-3">
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

                    <button type="submit" id="submit-btn" class="inline-flex items-center bg-blue-500 text-white px-5 py-3 rounded-xl shadow-md hover:bg-blue-600 dark:bg-blue-400 dark:hover:bg-blue-500 transition-colors duration-200 font-medium focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600">
                        <svg id="submit-spinner" class="hidden animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
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

        // optional previous period datasets (if controller provides)
        $prev_weekly = $data['prev_weekly'] ?? collect();
        $prev_monthly = $data['prev_monthly'] ?? collect();

        // chart-friendly arrays (will be JSON encoded for JS)
        $eggProduction = $data['eggProduction'] ?? collect();
        $eggProductionArr = $eggProduction->map(function($r) {
            return ['date' => $r->date ?? $r['date'] ?? null, 'value' => (float) ($r->value ?? $r['value'] ?? 0)];
        })->values();
    @endphp

    <!-- Report Panels -->
    <section class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700/50 overflow-x-auto relative">
        <!-- Weekly Report -->
        <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Weekly Egg Report</h3>
            <div class="mb-10 h-96 relative">
                <canvas id="weekly-chart" aria-hidden="true"></canvas>
                <div id="weekly-chart-no-data" class="hidden absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70">No chartable data or Chart.js is not available.</div>
            </div>

            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-700/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Week</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($weekly as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-150">
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->year }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->week }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @unless($weekly->isNotEmpty())
                    <div class="mt-4">
                        <div class="animate-pulse space-y-3">
                            @for($i=0;$i<5;$i++)
                                <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                            @endfor
                        </div>
                    </div>
                @endunless
            </div>
        </div>

        <!-- Monthly Report -->
        <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Monthly Egg Report</h3>
            <div class="mb-10 h-96 relative">
                <canvas id="monthly-chart" aria-hidden="true"></canvas>
            </div>

            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-700/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($monthly as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-150">
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->year }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @unless($monthly->isNotEmpty())
                    <div class="mt-4">
                        <div class="animate-pulse space-y-3">
                            @for($i=0;$i<5;$i++)
                                <div class="h-5 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                            @endfor
                        </div>
                    </div>
                @endunless
            </div>
        </div>

        <!-- Custom Report -->
        <div id="custom-panel" class="tab-panel {{ $reportType === 'custom' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Analytics Report</h3>

            <div class="grid gap-8">
                @if ($eggs->isNotEmpty())
                    <div class="mb-10">
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Eggs</h4>
                        <div class="h-80">
                            <canvas id="eggs-chart" aria-hidden="true"></canvas>
                            <div id="eggs-chart-no-data" class="hidden absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/70">No chartable data or Chart.js is not available.</div>
                        </div>
                        <div class="table-container mt-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700/80">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Laid</th>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Crates</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($eggs as $egg)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-150">
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $egg->date_laid->format('Y-m-d') }}</td>
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ number_format($egg->crates, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($sales->isNotEmpty())
                    <div class="mb-10">
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Sales</h4>
                        <div class="h-80"><canvas id="sales-chart" aria-hidden="true"></canvas></div>
                        <div class="table-container mt-6">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700/80">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total (₵)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($sales as $sale)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-150">
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->sale_date->format('Y-m-d') }}</td>
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->customer->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">
                                                {{ $sale->saleable_type == 'App\Models\Bird' ? ($sale->saleable->breed ?? 'N/A') : 'Egg Batch ' . ($sale->saleable_id ?? 'N/A') }}
                                            </td>
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $sale->quantity }}</td>
                                            <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($sale->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- others omitted for brevity -->
            </div>

            @if ($eggs->isEmpty() && $sales->isEmpty() && $expenses->isEmpty() && $payrolls->isEmpty() && $transactions->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-center py-10">No data available for the selected metrics.</p>
            @endif
        </div>

        <!-- Profitability Report -->
        <div id="profitability-panel" class="tab-panel {{ $reportType === 'profitability' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Profitability Report</h3>
            <div class="mb-10 h-96 relative">
                <canvas id="profitability-chart" aria-hidden="true"></canvas>
            </div>

            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                    <thead class="bg-gray-50 dark:bg-gray-700/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bird ID</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Breed</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales (₵)</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Feed Cost (₵)</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Operational Cost (₵)</th>
                            <th class="px-6 py-4 text-left text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profit (₵)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($profitability as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-150">
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->bird_id ?? 'N/A' }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ $row->breed }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ ucfirst($row->type) }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($row->sales, 2) }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($row->feed_cost, 2) }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-gray-800 dark:text-gray-200">₵ {{ number_format($row->operational_cost ?? ($row->total_expenses + $row->total_payroll ?? 0), 2) }}</td>
                                <td class="px-6 py-5 whitespace-nowrap {{ $row->profit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    ₵ {{ number_format($row->profit, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profit & Loss Report -->
        @role('admin')
        <div id="profit-loss-panel" class="tab-panel {{ $reportType === 'profit-loss' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Profit & Loss Report</h3>
            @if (!empty($profit_loss))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg transform transition-all hover:scale-105 duration-200">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Total Income</h4>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">₵ {{ number_format($profit_loss['total_income'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg transform transition-all hover:scale-105 duration-200">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Total Expenses</h4>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">₵ {{ number_format(($profit_loss['total_expenses'] ?? 0) + ($profit_loss['total_payroll'] ?? 0), 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg transform transition-all hover:scale-105 duration-200">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Profit/Loss</h4>
                        <p id="pl-value" class="text-3xl font-bold {{ ($profit_loss['profit_loss'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ₵ {{ number_format($profit_loss['profit_loss'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
                <div class="h-96"><canvas id="profit-loss-chart" aria-hidden="true"></canvas></div>
                <p class="mt-6 text-gray-600 dark:text-gray-400">Period: {{ $profit_loss['start'] ?? '' }} to {{ $profit_loss['end'] ?? '' }}</p>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-10">No data found for the selected period.</p>
            @endif
        </div>
        @endrole

        <!-- Forecast -->
        @role('admin')
        <div id="forecast-panel" class="tab-panel {{ $reportType === 'forecast' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Financial Forecast</h3>
            @if (!empty($forecast))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg transform transition-all hover:scale-105 duration-200">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Forecasted Income</h4>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">₵ {{ number_format($forecast['forecasted_income'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg transform transition-all hover:scale-105 duration-200">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Forecasted Expenses</h4>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">₵ {{ number_format($forecast['forecasted_expenses'] ?? 0, 2) }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-lg transform transition-all hover:scale-105 duration-200">
                        <h4 class="text-lg font-medium text-gray-800 dark:text-white">Forecasted Profit</h4>
                        <p id="forecast-profit" class="text-3xl font-bold {{ ($forecast['forecasted_profit'] ?? 0) >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ₵ {{ number_format($forecast['forecasted_profit'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
                <div class="h-96"><canvas id="forecast-chart" aria-hidden="true"></canvas></div>
                <p class="mt-6 text-gray-600 dark:text-gray-400">Based on past 6 months data with 5% income growth and 3% expense growth.</p>
            @else
                <p class="text-gray-600 dark:text-gray-400 text-center py-10">No forecast data available.</p>
            @endif
        </div>
        @endrole
    </section>
</div>

<!-- Advanced Export Modal -->
<div id="advanced-export-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-8 shadow-xl">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Advanced Export</h3>
        <p class="text-base text-gray-600 dark:text-gray-300 mt-2">Select columns, format and extra options for the export.</p>

        <!-- Use GET so the route can remain unchanged (server accepts GET in your ReportController->export) -->
        <form id="advanced-export-form" method="GET" action="" class="mt-6" target="_blank">
            <input type="hidden" name="type" value="{{ $reportType }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <fieldset>
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300">Columns</legend>
                    <div class="mt-3 space-y-3">
                        <label class="flex items-center"><input type="checkbox" name="columns[]" value="date" checked class="mr-2 rounded focus:ring-blue-500"> Date</label>
                        <label class="flex items-center"><input type="checkbox" name="columns[]" value="description" class="mr-2 rounded focus:ring-blue-500"> Description</label>
                        <label class="flex items-center"><input type="checkbox" name="columns[]" value="amount" class="mr-2 rounded focus:ring-blue-500"> Amount</label>
                        <label class="flex items-center"><input type="checkbox" name="columns[]" value="customer" class="mr-2 rounded focus:ring-blue-500"> Customer</label>
                        <label class="flex items-center"><input type="checkbox" name="columns[]" value="quantity" class="mr-2 rounded focus:ring-blue-500"> Quantity</label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300">Options</legend>
                    <div class="mt-3 space-y-3">
                        <label class="flex items-center"><input type="checkbox" name="include_chart" value="1" class="mr-2 rounded focus:ring-blue-500"> Include charts (PDF/Excel only)</label>
                        <label class="flex items-center"><input type="checkbox" name="include_summary" value="1" class="mr-2 rounded focus:ring-blue-500"> Include KPI summary</label>
                        <label class="flex items-center"><input type="checkbox" name="separate_sheets" value="1" class="mr-2 rounded focus:ring-blue-500"> Separate sheets per metric (Excel)</label>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm">Format</label>
                        <select name="format" class="mt-2 w-full border rounded-lg p-3 dark:bg-gray-800 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition duration-200">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </fieldset>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="button" id="advanced-export-cancel" class="px-5 py-3 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">Cancel</button>
                <button type="submit" id="advanced-export-submit" class="px-5 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors duration-200">Export</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
/* shimmer for KPI cards (used when loading) */
.kpi-skel {
  background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0) 100%);
  background-size: 200% 100%;
  animation: shimmer 1.2s linear infinite;
  opacity: .6;
}
@keyframes shimmer {
  0% { background-position: -100% 0; }
  100% { background-position: 100% 0; }
}

/* table skeleton rows */
.table-skel-row { display: block; height: 1rem; margin: .5rem 0; border-radius: .375rem; background: linear-gradient(90deg,#e5e7eb,#f3f4f6,#e5e7eb); background-size: 200% 100%; animation: shimmer 1.2s linear infinite; }

.tab-panel.hidden { display: none; }

/* Chart no-js overlay */
.chart-no-js {
    position: absolute;
    inset: 0;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(255,255,255,0.85);
    color: #4b5563; /* gray-600 */
    font-weight:600;
}
</style>
@endpush

@push('scripts')
<script>
(() => {
    // Small helpers
    const $ = s => document.querySelector(s);
    const $$ = s => Array.from(document.querySelectorAll(s));
    const routeExport = (params = {}) => {
        // Use your existing reports.export route (GET); we append query params
        const base = "";
        const qp = new URLSearchParams(params);
        return base + '?' + qp.toString();
    };

    // keep server data available in JS (safe JSON)
    const server = {
        eggProduction: @json($eggProductionArr),
        weekly: @json($weekly),
        monthly: @json($monthly),
        eggs: @json($eggs),
        sales: @json($sales),
        profit_loss: @json($profit_loss),
        reportType: "{{ $reportType }}",
    };

    /* ----------------- UI wiring ----------------- */
    // tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            document.getElementById('report-type').value = tab;
            // set active states (visual only)
            document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('bg-blue-500','text-white'));
            btn.classList.add('bg-blue-500','text-white');
            // toggle panels
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            const panel = document.getElementById(`${tab}-panel`);
            if (panel) panel.classList.remove('hidden');
            // if switching to custom show metrics area
            document.getElementById('metrics-section').classList.toggle('hidden', tab !== 'custom');
        });
    });

    // presets
    $$('.preset-btn').forEach(b => {
        b.addEventListener('click', (e) => {
            const days = parseInt(b.dataset.days, 10);
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));
            $('#start_date').value = start.toISOString().slice(0,10);
            $('#end_date').value = end.toISOString().slice(0,10);
            // submit form
            $('#report-filter-form').submit();
        });
    });

    // toggles
    $('#compare-toggle').addEventListener('change', (e) => {
        $('#compare-field').value = e.target.checked ? '1' : '0';
    });
    $('#cumulative-toggle').addEventListener('change', (e) => {
        $('#cumulative-field').value = e.target.checked ? '1' : '0';
    });

    // Export dropdown toggle + outside click to close
    const exportBtn = $('#export-btn');
    const exportDropdown = $('#export-dropdown');
    exportBtn.addEventListener('click', (e) => {
        e.preventDefault();
        exportDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', (e) => {
        if (!exportDropdown.contains(e.target) && !exportBtn.contains(e.target)) {
            exportDropdown.classList.add('hidden');
        }
    });

    // export option handlers (GET links)
    $$('.export-option').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const fmt = btn.dataset.format;
            // gather current filters
            const params = gatherFilters();
            params.format = fmt;
            // open in new tab so downloads happen naturally
            window.open(routeExport(params), '_blank');
            exportDropdown.classList.add('hidden');
        });
    });

    // Advanced export
    $('#export-advanced').addEventListener('click', () => {
        $('#advanced-export-modal').classList.remove('hidden');
        $('#advanced-export-modal').classList.add('flex');
    });
    $('#advanced-export-cancel').addEventListener('click', () => {
        $('#advanced-export-modal').classList.add('hidden');
        $('#advanced-export-modal').classList.remove('flex');
    });

    // Advanced export form: augment with current filters before submit
    $('#advanced-export-form').addEventListener('submit', function (e) {
        // form is GET and target=_blank, but copy filters to form as hidden inputs
        const filters = gatherFilters();
        for (const [k,v] of Object.entries(filters)) {
            let input = this.querySelector(`input[name="${k}"]`);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = k;
                this.appendChild(input);
            }
            input.value = v;
        }
        // Allow normal submission (opens in new tab)
    });

    // Download visible table to CSV (client-side)
    $('#download-csv').addEventListener('click', () => {
        const visiblePanel = Array.from(document.querySelectorAll('.tab-panel')).find(p => !p.classList.contains('hidden'));
        if (!visiblePanel) return alert('No visible report to download.');
        const table = visiblePanel.querySelector('table');
        if (!table) return alert('No table found in the visible report.');
        const rows = [];
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        rows.push(headers);
        table.querySelectorAll('tbody tr').forEach(tr => {
            const cols = Array.from(tr.querySelectorAll('td')).map(td => td.textContent.trim());
            rows.push(cols);
        });
        const csv = rows.map(r => r.map(cell => `"${String(cell).replace(/"/g,'""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const filename = `report_{{ $reportType }}_${(new Date()).toISOString().slice(0,10)}.csv`;
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Utility: gather filters from form
    function gatherFilters() {
        const params = {};
        params.type = document.getElementById('report-type').value || '{{ $reportType }}';
        const sd = document.getElementById('start_date').value;
        const ed = document.getElementById('end_date').value;
        if (sd) params.start_date = sd;
        if (ed) params.end_date = ed;
        if (document.getElementById('compare-field').value === '1') params.compare = 1;
        if (document.getElementById('cumulative-field').value === '1') params.cumulative = 1;
        // metrics (custom)
        const metrics = Array.from(document.querySelectorAll('input[name="metrics[]"]:checked')).map(n => n.value);
        if (metrics.length) params.metrics = metrics;
        return params;
    }

    /* ----------------- Chart rendering (if Chart.js present) ----------------- */
    // Chart generation helper: safe guard for missing Chart.js (offline)
    function buildChart(ctx, cfg) {
        try {
            if (!window.Chart) throw new Error('Chart.js not detected');
            return new Chart(ctx, cfg);
        } catch (err) {
            console.warn('Chart render skipped:', err.message);
            // show no-js overlays for the canvas parent
            const parent = ctx.canvas.parentElement;
            if (parent && parent.querySelector) {
                const fallback = parent.querySelector('.chart-no-js') || document.createElement('div');
                fallback.className = 'chart-no-js';
                fallback.textContent = 'Chart rendering unavailable — include local Chart.js to enable charts.';
                if (!parent.contains(fallback)) parent.appendChild(fallback);
            }
            return null;
        }
    }

    // Normalize server eggProduction into labels/data arrays
    function prepareEggProduction() {
        const arr = server.eggProduction || [];
        if (!arr.length) return { labels: [], data: [] };
        // sort by date
        arr.sort((a,b) => (a.date > b.date ? 1 : (a.date < b.date ? -1 : 0)));
        const labels = arr.map(x => x.date);
        const data = arr.map(x => Number(x.value || 0));
        return { labels, data };
    }

    function renderCharts() {
        // Weekly chart (eggProduction)
        const weeklyCtx = document.getElementById('weekly-chart');
        if (weeklyCtx) {
            const prepared = prepareEggProduction();
            if (prepared.labels.length === 0) {
                document.getElementById('weekly-chart-no-data').classList.remove('hidden');
            } else {
                document.getElementById('weekly-chart-no-data').classList.add('hidden');
            }
            const cfg = {
                type: 'line',
                data: {
                    labels: prepared.labels,
                    datasets: [{
                        label: 'Egg crates',
                        data: prepared.data,
                        fill: false,
                        tension: 0.2,
                        pointRadius: 3,
                        borderWidth: 2,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { display: true, title: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            };
            buildChart(weeklyCtx, cfg);
        }

        // Eggs chart on custom panel
        const eggsCtx = document.getElementById('eggs-chart');
        if (eggsCtx) {
            // try to derive labels and data from server.eggs if present
            let labels = [], data = [];
            if (server.eggs && server.eggs.length) {
                server.eggs.forEach(e => { labels.push(e.date_laid ?? e.date ?? ''); data.push(Number(e.crates ?? e.value ?? 0)); });
            } else {
                labels = prepareEggProduction().labels;
                data = prepareEggProduction().data;
            }
            if (!labels.length) {
                const nod = document.getElementById('eggs-chart-no-data');
                if (nod) nod.classList.remove('hidden');
            }
            buildChart(eggsCtx, {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Egg crates', data: data, borderWidth: 0 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        }

        // Sales & other charts can be added similarly if server.sales present
        const salesCtx = document.getElementById('sales-chart');
        if (salesCtx && server.sales && server.sales.length) {
            const labels = server.sales.map(s => s.sale_date ?? s.date ?? '');
            const data = server.sales.map(s => Number(s.total_amount ?? 0));
            buildChart(salesCtx, { type: 'line', data: { labels, datasets: [{ label: 'Sales', data }] }, options: { maintainAspectRatio: false } });
        }

        // Profit-loss & forecast charts: if you want to show timeseries, prepare data similarly
        // Minimal stub to avoid errors
    }

    // attempt to render charts when DOM ready
    document.addEventListener('DOMContentLoaded', function () {
        renderCharts();
    });

    /* ----------------- Small UX helpers ----------------- */
    function toast(msg, timeout = 3000) {
        const t = $('#toast');
        const inner = $('#toastInner span');
        inner.textContent = msg;
        t.classList.remove('invisible');
        t.classList.remove('opacity-0');
        t.classList.add('opacity-100');
        t.classList.add('pointer-events-auto');
        setTimeout(() => {
            t.classList.add('opacity-0');
            t.classList.remove('opacity-100');
            setTimeout(() => {
                t.classList.add('invisible');
                t.classList.remove('pointer-events-auto');
            }, 400);
        }, timeout);
    }
})();
</script>
@endpush

@endsection
