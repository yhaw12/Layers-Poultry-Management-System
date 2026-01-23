@extends('layouts.app')

@section('content')
<style>
    /* Utility to hide scrollbar while allowing scrolling */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>

{{-- Main Container: added overflow-hidden to prevent horizontal scroll on body --}}
<div class="container mx-auto px-4 py-4 sm:py-8 space-y-6 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300 overflow-x-hidden">
    
    {{-- Toast Notification --}}
    <div id="toast" class="fixed right-4 top-4 sm:right-6 sm:top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4">
        <div id="toastInner" class="max-w-[90vw] sm:max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 dark:bg-gray-700 text-white flex items-center space-x-3 border border-gray-700 dark:border-gray-600">
            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="toastMessage" class="font-medium text-sm truncate"></span>
        </div>
    </div>

    {{-- Header & Filters --}}
    <section class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 sm:gap-6">
        <div class="flex-1 w-full">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Farm Analytics</h2>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-1">Deep dive into trends, financials, and efficiency.</p>

            {{-- Mobile scrollable filter buttons --}}
            <div class="mt-4 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-4 px-4 sm:mx-0 sm:px-0">
                @foreach([7 => '7d', 30 => '30d', 90 => '3M', 180 => '6M', 365 => 'YTD'] as $days => $label)
                    <button type="button" class="preset-btn flex-shrink-0 inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200" data-days="{{ $days }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Export Button --}}
        <div class="w-1/6 flex-shrink-0">
            <div class="relative group w-full lg:w-auto">
                <button id="export-btn" class="w-full lg:w-auto justify-center inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 sm:py-2.5 rounded-lg shadow-md transition-all duration-200 font-medium text-sm">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Report
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-full lg:w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-20 border border-gray-200 dark:border-gray-700 overflow-hidden transform origin-top-right transition-all">
                    <button data-format="pdf" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">PDF Report</button>
                    <button data-format="excel" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Excel Spreadsheet</button>
                </div>
            </div>
        </div>
    </section>

    {{-- Main KPI Cards --}}
    <section id="kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $profit_loss = $data['profit_loss'] ?? [];
            $totals = $data['totals'] ?? [];
        @endphp

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Income</div>
            <div class="text-2xl font-bold mt-2 text-gray-900 dark:text-white truncate">₵ {{ number_format($profit_loss['total_income'] ?? ($totals['income'] ?? 0), 2) }}</div>
            <div class="absolute right-4 top-4 p-2 bg-green-50 dark:bg-green-900/30 rounded-full">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expenses</div>
            <div class="text-2xl font-bold mt-2 text-gray-900 dark:text-white truncate">₵ {{ number_format(($profit_loss['total_expenses'] ?? 0) + ($profit_loss['total_payroll'] ?? 0), 2) }}</div>
            <div class="absolute right-4 top-4 p-2 bg-red-50 dark:bg-red-900/30 rounded-full">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</div>
            <div class="text-2xl font-bold mt-2 truncate {{ (($profit_loss['profit_loss'] ?? 0) >= 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                ₵ {{ number_format($profit_loss['profit_loss'] ?? ($totals['profit'] ?? 0), 2) }}
            </div>
            <div class="absolute right-4 top-4 p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Production</div>
            <div class="text-2xl font-bold mt-2 text-gray-900 dark:text-white truncate">{{ number_format($data['avg_crates_per_day'] ?? 0, 1) }} <span class="text-sm font-normal text-gray-500">cr/day</span></div>
            <div class="absolute right-4 top-4 p-2 bg-amber-50 dark:bg-amber-900/30 rounded-full">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
        </div>
    </section>

    {{-- Executive Intelligence --}}
    <section>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Economic FCR</div>
                <div class="text-xl sm:text-2xl font-black text-blue-600 mt-1 truncate">₵{{ number_format($data['advanced_metrics']['economic_fcr'] ?? 0, 2) }}</div>
                <div class="hidden sm:block text-[10px] text-gray-500 mt-1">Feed cost per ₵100 revenue.</div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Production Gap</div>
                <div class="text-xl sm:text-2xl font-black {{ ($data['advanced_metrics']['production_gap'] ?? 0) > 15 ? 'text-red-500' : 'text-green-500' }} mt-1">
                    {{ $data['advanced_metrics']['production_gap'] ?? 0 }}%
                </div>
                <div class="hidden sm:block text-[10px] text-gray-500 mt-1">Distance from 85% lay target.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dead Money</div>
                <div class="text-xl sm:text-2xl font-black text-orange-600 mt-1 truncate">₵{{ number_format($data['advanced_metrics']['dead_money'] ?? 0, 0) }}</div>
                <div class="hidden sm:block text-[10px] text-gray-500 mt-1">Value of unsold inventory.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Labor ROI</div>
                <div class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $data['advanced_metrics']['labor_efficiency'] ?? '0.00' }}x</div>
                <div class="hidden sm:block text-[10px] text-gray-500 mt-1">Revenue per ₵1.00 wages.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 col-span-2 lg:col-span-1">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock Age</div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white">{{ $data['advanced_metrics']['stock_aging_days'] ?? 0 }}d</span>
                    <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold uppercase {{ ($data['advanced_metrics']['spoilage_risk'] ?? '') === 'High' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $data['advanced_metrics']['spoilage_risk'] ?? 'Low' }}</span>
                </div>
                <div class="hidden sm:block text-[10px] text-gray-500 mt-1">Avg days before sale.</div>
            </div>
        </div>
    </section>

    {{-- Main Report Body --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden w-full">
        
        {{-- Filter & Tab Form --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <form id="report-filter-form" method="GET" action="{{ route('reports.index') }}">
                @csrf
                
                {{-- 
                    OPTIMIZED TABS FOR MOBILE:
                    1. Flex-nowrap prevents wrapping
                    2. overflow-x-auto allows scrolling
                    3. no-scrollbar hides the ugly bar
                    4. -mx-4 px-4 creates "bleed" effect so tabs scroll to edge of screen but stay aligned
                --}}
                <div class="flex flex-nowrap overflow-x-auto pb-2 mb-4 gap-2 no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0 items-center">
                    @foreach (['trends', 'comparisons', 'weekly', 'monthly', 'payments', 'efficiency', 'forecast'] as $tab)
                        <button type="button" data-tab="{{ $tab }}"
                                class="tab-btn flex-shrink-0 whitespace-nowrap px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 border
                                {{ $reportType === $tab 
                                    ? 'bg-gray-800 dark:bg-blue-600 text-white border-gray-800 dark:border-blue-600 shadow-md' 
                                    : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600' }}">
                            {{ ucfirst($tab) }}
                        </button>
                    @endforeach
                    <input type="hidden" name="type" id="report-type" value="{{ $reportType }}">
                    <input type="hidden" name="compare" id="compare-field" value="{{ request('compare', '0') }}">
                </div>

                <div class="flex flex-col lg:flex-row gap-4 items-end">
                    <div class="w-1/6 flex-1">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Date Range</label>
                        <div class="flex flex-col sm:flex-row gap-2 w-1/6">
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $data['profit_loss']['start'] ?? now()->subDays(30)->toDateString()) }}" class="w-1/ px-4 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 border border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                            
                            <span class="text-xs text-gray-400 text-center self-center py-1 sm:py-0">to</span>
                            
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $data['profit_loss']['end'] ?? now()->toDateString()) }}" class="w-full px-4 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 border border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full lg:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition shadow-sm">Update View</button>
                </div>
            </form>
        </div>

        {{-- Panel Content --}}
        <div class="p-4 sm:p-6 min-h-[500px]">
            
            {{-- TAB: TRENDS --}}
            <div id="trends-panel" class="tab-panel {{ $reportType === 'trends' ? '' : 'hidden' }}">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Performance Trends</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach([
                        ['id' => 'eggTrend', 'title' => 'Egg Production', 'selectId' => 'eggChartType'],
                        ['id' => 'feedTrend', 'title' => 'Feed Consumption', 'selectId' => 'feedChartType'],
                        ['id' => 'salesTrend', 'title' => 'Sales Volume', 'selectId' => 'salesChartType'],
                        ['id' => 'incomeChart', 'title' => 'Income Flow', 'selectId' => 'incomeChartType']
                    ] as $chart)
                    <div class="chart-container bg-white dark:bg-gray-700/30 rounded-xl p-4 border border-gray-200 dark:border-gray-700 w-full min-w-0">
                        <div class="flex justify-between mb-2">
                            <h4 class="font-bold text-sm text-gray-700 dark:text-gray-200">{{ $chart['title'] }}</h4>
                            <select id="{{ $chart['selectId'] }}" class="text-xs rounded border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"><option value="line">Line</option><option value="bar">Bar</option></select>
                        </div>
                        <div class="h-64 relative w-full"><canvas id="{{ $chart['id'] }}"></canvas></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TAB: COMPARISONS --}}
            <div id="comparisons-panel" class="tab-panel {{ $reportType === 'comparisons' ? '' : 'hidden' }}">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Strategic Comparisons</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0">
                        <div class="flex justify-between mb-4">
                            <h4 class="font-bold text-lg text-gray-700 dark:text-white">Sales: Eggs vs Birds</h4>
                            <select id="salesComparisonChartType" class="text-xs rounded border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"><option value="line">Line</option><option value="bar">Bar</option></select>
                        </div>
                        <div class="h-64 sm:h-80 relative w-full"><canvas id="salesComparison"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- TAB: PAYMENTS --}}
            <div id="payments-panel" class="tab-panel {{ $reportType === 'payments' ? '' : 'hidden' }}">
                <div class="mb-8 p-6 dark:bg-gray-700/30 from-indigo-50 to-white dark:from-indigo-950 dark:to-gray-900 rounded-xl border border-indigo-100 dark:border-indigo-800/50 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-300 uppercase tracking-widest mb-1">Total Transaction Volume</h4>
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white">
                            ₵ {{ number_format($data['totalTransactions'] ?? 0, 2) }}
                        </div>
                        <p class="text-xs text-gray-500 dark:text-indigo-200/70 mt-1">Sum of all recorded transactions in this period.</p>
                    </div>
                    <div class="hidden sm:block p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-full text-indigo-600 dark:text-indigo-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 col-span-1 lg:col-span-2 w-full min-w-0">
                        <div class="flex justify-between mb-4">
                            <h4 class="font-bold text-lg text-gray-800 dark:text-white">Transaction History</h4>
                            <select id="transactionChartType" class="text-xs rounded border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"><option value="line">Line</option><option value="bar">Bar</option></select>
                        </div>
                        <div class="h-64 relative w-full"><canvas id="transactionTrend"></canvas></div>
                    </div>

                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0">
                        <div class="flex justify-between mb-4">
                            <h4 class="font-bold text-lg text-gray-800 dark:text-white">Invoice Status</h4>
                            <select id="invoiceChartType" class="text-xs rounded border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600"><option value="bar">Bar</option><option value="pie">Pie</option></select>
                        </div>
                        <div class="h-64 relative w-full"><canvas id="invoiceStatus"></canvas></div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0">
                        <h4 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">Payment Methods</h4>
                        <div class="h-64 relative w-full"><canvas id="method-doughnut-chart"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- TAB: WEEKLY --}}
            <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Weekly Production</h3>
                <div class="h-64 sm:h-80 mb-6 relative w-full min-w-0"><canvas id="weekly-chart"></canvas></div>
            </div>

            {{-- TAB: MONTHLY --}}
            <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
                 <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Monthly Overview</h3>
                 <div class="h-64 sm:h-80 mb-6 relative w-full min-w-0"><canvas id="monthly-chart"></canvas></div>
            </div>
            
            {{-- TAB: EFFICIENCY --}}
            <div id="efficiency-panel" class="tab-panel {{ $reportType === 'efficiency' ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                      <div class="p-4 sm:p-5 border rounded-xl dark:border-gray-700 w-full min-w-0"><div class="h-64 relative w-full"><canvas id="mortality-chart"></canvas></div></div>
                      <div class="p-4 sm:p-5 border rounded-xl dark:border-gray-700 w-full min-w-0"><div class="h-64 relative w-full"><canvas id="expense-pie-chart"></canvas></div></div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
/**
 * 1. ROBUST DATA NORMALIZATION
 */
function normalizeSeries(raw) {
    try {
        if (!raw) return { labels: [], values: [] };
        if (typeof raw === 'string') raw = JSON.parse(raw);
        
        if (!Array.isArray(raw) && typeof raw === 'object') {
            const keys = Object.keys(raw);
            return { labels: keys, values: keys.map(k => Number(raw[k] ?? 0)) };
        }
        
        if (Array.isArray(raw) && raw.length > 0) {
            const first = raw[0];
            if (first && typeof first === 'object') {
                return {
                    labels: raw.map(r => r.date ?? r.label ?? r.week ?? r.category ?? r.egg_size ?? ''),
                    values: raw.map(r => Number(r.value ?? r.total ?? r.amount ?? r.crates ?? 0))
                };
            }
            return { labels: raw.map((_, i) => i + 1), values: raw.map(v => Number(v)) };
        }
    } catch (e) {
        console.error("Normalization Error:", e);
    }
    return { labels: [], values: [] };
}

/**
 * 2. DATA PAYLOAD
 */
const RAW_DATA = {
    transactionTrend: @json($data['charts']['transactionTrend'] ?? []),
    eggTrend: @json($data['charts']['eggTrend'] ?? []),
    feedTrend: @json($data['charts']['feedTrend'] ?? []),
    salesTrend: @json($data['charts']['salesTrend'] ?? []),
    incomeTrend: @json($data['charts']['incomeTrend'] ?? []),
    salesComparison: @json($data['charts']['salesComparison'] ?? []),
    invoiceStatuses: @json($data['charts']['invoiceStatuses'] ?? []),
    mortalityTrend: @json($data['efficiency']['mortality_trend'] ?? []),
    expenseBreakdown: @json($data['efficiency']['expense_breakdown'] ?? []),
    eggGrades: @json($data['efficiency']['egg_grades'] ?? []),
    weekly: @json($data['weekly'] ?? []),
    monthly: @json($data['monthly'] ?? []),
    payments: @json($data['payments'] ?? [])
};

const COLORS = {
    blue: '#3B82F6', green: '#10B981', red: '#EF4444', 
    yellow: '#F59E0B', purple: '#8B5CF6', teal: '#14B8A6',
    gray: '#9ca3af'
};

/**
 * 3. CHART CREATION HELPER
 */
function safeCreateChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const existing = Chart.getChart(canvas);
    if (existing) existing.destroy();

    const isDark = document.documentElement.classList.contains('dark');
    
    // VISIBILITY FIX: Darker colors for light mode
    const textColor = isDark ? '#D1D5DB' : '#1f2937'; // Gray-300 vs Gray-800 (nearly black)
    const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'; 
    
    const defaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                display: config.showLegend ?? false,
                labels: { color: textColor }
            }
        },
        scales: config.type !== 'pie' && config.type !== 'doughnut' ? {
            y: { 
                beginAtZero: true,
                ticks: { color: textColor },
                grid: { color: gridColor }
            },
            x: { 
                ticks: { color: textColor },
                grid: { display: false }
            }
        } : {}
    };

    return new Chart(canvas.getContext('2d'), {
        type: config.type,
        data: config.data,
        options: Object.assign(defaults, config.options ?? {})
    });
}

/**
 * 4. INITIALIZATION
 */
document.addEventListener('DOMContentLoaded', () => {
    // A. Trends
    ['eggTrend', 'feedTrend', 'salesTrend', 'incomeChart'].forEach(id => {
        const key = id === 'incomeChart' ? 'incomeTrend' : id;
        const series = normalizeSeries(RAW_DATA[key]);
        window[id] = safeCreateChart(id, {
            type: 'line',
            data: {
                labels: series.labels,
                datasets: [{
                    data: series.values,
                    borderColor: id.includes('feed') ? COLORS.yellow : COLORS.blue,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true, tension: 0.3
                }]
            }
        });
    });

    // B. Comparisons
    if (RAW_DATA.salesComparison.length) {
        const sc = RAW_DATA.salesComparison;
        window.salesComparison = safeCreateChart('salesComparison', {
            type: 'line', showLegend: true,
            data: {
                labels: sc.map(r => r.date),
                datasets: [
                    { label: 'Eggs', data: sc.map(r => r.egg_sales), borderColor: COLORS.green, tension: 0.3 },
                    { label: 'Birds', data: sc.map(r => r.bird_sales), borderColor: COLORS.blue, tension: 0.3 }
                ]
            }
        });
    }

    // C. Periodics
    const w = normalizeSeries(RAW_DATA.weekly);
    safeCreateChart('weekly-chart', { type: 'bar', data: { labels: w.labels, datasets: [{ data: w.values, backgroundColor: COLORS.blue }] } });

    const m = normalizeSeries(RAW_DATA.monthly);
    safeCreateChart('monthly-chart', { type: 'bar', data: { labels: m.labels, datasets: [{ data: m.values, backgroundColor: COLORS.purple }] } });

    // D. Efficiency
    const mort = normalizeSeries(RAW_DATA.mortalityTrend);
    safeCreateChart('mortality-chart', { type: 'line', data: { labels: mort.labels, datasets: [{ data: mort.values, borderColor: COLORS.red }] } });

    const exp = normalizeSeries(RAW_DATA.expenseBreakdown);
    safeCreateChart('expense-pie-chart', { type: 'doughnut', showLegend: true, data: { labels: exp.labels, datasets: [{ data: exp.values, backgroundColor: [COLORS.blue, COLORS.red, COLORS.green, COLORS.yellow] }] } });

    // E. Invoice, Payments & Transactions
    const inv = normalizeSeries(RAW_DATA.invoiceStatuses);
    window.invoiceStatusChart = safeCreateChart('invoiceStatus', { type: 'bar', data: { labels: inv.labels, datasets: [{ data: inv.values, backgroundColor: [COLORS.yellow, COLORS.green, COLORS.purple, COLORS.red] }] } });

    if (RAW_DATA.payments && RAW_DATA.payments.chartData) {
        safeCreateChart('method-doughnut-chart', { 
            type: 'doughnut', showLegend: true, 
            data: { labels: RAW_DATA.payments.chartLabels, datasets: [{ data: RAW_DATA.payments.chartData, backgroundColor: [COLORS.green, COLORS.blue, COLORS.purple] }] } 
        });
    }

    const txSeries = normalizeSeries(RAW_DATA.transactionTrend);
    window.transactionChart = safeCreateChart('transactionTrend', {
        type: 'line',
        data: {
            labels: txSeries.labels,
            datasets: [{
                label: 'Transaction Amount',
                data: txSeries.values,
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    /**
     * 5. UI CONTROLS - UPDATED TAB LOGIC
     */
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.dataset.tab;
            
            // Reset all tabs to inactive state
            document.querySelectorAll('.tab-btn').forEach(b => {
                // Remove Active Classes
                b.classList.remove('bg-gray-800', 'dark:bg-blue-600', 'text-white', 'shadow-md', 'border-gray-800', 'dark:border-blue-600');
                // Add Inactive Classes
                b.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300', 'border-gray-200', 'dark:border-gray-600', 'hover:bg-gray-100');
            });

            // Set clicked tab to active state
            this.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300', 'border-gray-200', 'dark:border-gray-600', 'hover:bg-gray-100');
            this.classList.add('bg-gray-800', 'dark:bg-blue-600', 'text-white', 'shadow-md', 'border-gray-800', 'dark:border-blue-600');
            
            // Switch Panels
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            const panel = document.getElementById(target + '-panel');
            if(panel) panel.classList.remove('hidden');

            document.getElementById('report-type').value = target;
        });
    });

    // Chart Type Toggles
    const typeSelectors = {
        'eggChartType': 'eggTrend',
        'feedChartType': 'feedTrend',
        'salesChartType': 'salesTrend',
        'incomeChartType': 'incomeChart',
        'invoiceChartType': 'invoiceStatusChart',
        'transactionChartType': 'transactionChart'
    };

    Object.keys(typeSelectors).forEach(selectId => {
        const el = document.getElementById(selectId);
        if (el) {
            el.addEventListener('change', (e) => {
                const chartInstance = window[typeSelectors[selectId]];
                if (chartInstance) {
                    chartInstance.config.type = e.target.value;
                    chartInstance.update();
                }
            });
        }
    });

    // Date Presets
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const days = parseInt(btn.dataset.days);
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - days);
            
            document.getElementById('start_date').value = start.toISOString().split('T')[0];
            document.getElementById('end_date').value = end.toISOString().split('T')[0];
            document.getElementById('report-filter-form').submit();
        });
    });
});
</script>
@endpush
@endsection