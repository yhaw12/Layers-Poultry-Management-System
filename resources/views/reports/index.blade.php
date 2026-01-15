{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    
    <div id="toast" class="fixed right-6 top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4">
        <div id="toastInner" class="max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 dark:bg-gray-700 text-white flex items-center space-x-3 border border-gray-700 dark:border-gray-600">
            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium"></span>
        </div>
    </div>

    <section class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="flex-1">
            <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white tracking-tight">Farm Analytics</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-2">Comprehensive reports and insights for your operations.</p>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                @foreach([7 => '7d', 30 => '30d', 180 => '6M', 365 => 'YTD'] as $days => $label)
                    <button type="button" class="preset-btn inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200" data-days="{{ $days }}">
                        {{ $label }}
                    </button>
                @endforeach

                <div class="h-6 w-px bg-gray-300 dark:bg-gray-700 mx-2"></div>

                <label class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 cursor-pointer select-none">
                    <input id="compare-toggle" type="checkbox" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition duration-150 ease-in-out" /> 
                    <span class="ml-2">Compare Previous</span>
                </label>
            </div>
        </div>

        <div class="flex-shrink-0 flex space-x-3 items-center">
            <div class="relative group">
                <button id="export-btn" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg shadow-md transition-all duration-200 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:ring-offset-gray-900">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-20 border border-gray-200 dark:border-gray-700 overflow-hidden transform origin-top-right transition-all">
                    <button data-format="pdf" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">PDF Report</button>
                    <button data-format="excel" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">Excel Spreadsheet</button>
                    <button data-format="csv" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">CSV Data</button>
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                    <button id="export-advanced" class="w-full text-left px-4 py-3 text-sm text-blue-600 dark:text-blue-400 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Advanced Options...</button>
                </div>
            </div>
        </div>
    </section>

    <section id="kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $profit_loss = $data['profit_loss'] ?? [];
            $totals = $data['totals'] ?? [];
            // Helper to format date for display logic in view
            $formatDate = function($d) { return $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '-'; };
        @endphp

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Income</div>
            <div id="kpi-income" class="text-2xl font-bold mt-2 text-gray-900 dark:text-white">₵ {{ number_format($profit_loss['total_income'] ?? ($totals['income'] ?? 0), 2) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ $formatDate($profit_loss['start'] ?? null) }} — {{ $formatDate($profit_loss['end'] ?? null) }}
            </div>
            <div class="absolute right-4 top-4 p-2 bg-green-50 dark:bg-green-900/30 rounded-full">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-200 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expenses</div>
            <div id="kpi-expenses" class="text-2xl font-bold mt-2 text-gray-900 dark:text-white">₵ {{ number_format(($profit_loss['total_expenses'] ?? 0) + ($profit_loss['total_payroll'] ?? 0), 2) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">Includes payroll</div>
            <div class="absolute right-4 top-4 p-2 bg-red-50 dark:bg-red-900/30 rounded-full">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-200 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</div>
            <div id="kpi-profit" class="text-2xl font-bold mt-2 {{ (($profit_loss['profit_loss'] ?? 0) >= 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                ₵ {{ number_format($profit_loss['profit_loss'] ?? ($totals['profit'] ?? 0), 2) }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">Net calculation</div>
            <div class="absolute right-4 top-4 p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-200 relative overflow-hidden">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Production</div>
            <div id="kpi-avg" class="text-2xl font-bold mt-2 text-gray-900 dark:text-white">{{ number_format($data['avg_crates_per_day'] ?? 0, 1) }} <span class="text-sm font-normal text-gray-500">crates/day</span></div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">Efficiency Metric</div>
            <div class="absolute right-4 top-4 p-2 bg-amber-50 dark:bg-amber-900/30 rounded-full">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
        </div>
    </section>
    {{-- Updated Advanced Intelligence Section --}}
    <section class="mt-8 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Executive Farm Intelligence
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Economic FCR</div>
                <div class="text-2xl font-black text-blue-600 mt-1">₵{{ number_format($data['advanced_metrics']['economic_fcr'] ?? 0, 2) }}</div>
                <div class="text-[10px] text-gray-500 mt-1">Feed cost per ₵100 revenue.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Production Gap</div>
                <div class="text-2xl font-black {{ ($data['advanced_metrics']['production_gap'] ?? 0) > 15 ? 'text-red-500' : 'text-green-500' }} mt-1">
                    {{ $data['advanced_metrics']['production_gap'] ?? 0 }}%
                </div>
                <div class="text-[10px] text-gray-500 mt-1">Distance from 85% lay target.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dead Money</div>
                <div class="text-2xl font-black text-orange-600 mt-1">₵{{ number_format($data['advanced_metrics']['dead_money'] ?? 0, 0) }}</div>
                <div class="text-[10px] text-gray-500 mt-1">Value of unsold inventory.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Labor ROI</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $data['advanced_metrics']['labor_efficiency'] ?? '0.00' }}x</div>
                <div class="text-[10px] text-gray-500 mt-1">Revenue per ₵1.00 wages.</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock Age</div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $data['advanced_metrics']['stock_aging_days'] ?? 0 }}d</span>
                    <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold uppercase 
                        {{ ($data['advanced_metrics']['spoilage_risk'] ?? '') === 'High' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $data['advanced_metrics']['spoilage_risk'] ?? 'Low' }}
                    </span>
                </div>
                <div class="text-[10px] text-gray-500 mt-1">Avg days before sale.</div>
            </div>

        </div>
    </section>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <form id="report-filter-form" method="GET" action="{{ route('reports.index') }}">
                @csrf
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach (['weekly', 'monthly', 'efficiency', 'payments', 'profitability', 'forecast', 'custom'] as $tab)
                        <button type="button" data-tab="{{ $tab }}"
                                class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border 
                                {{ $reportType === $tab 
                                    ? 'bg-blue-600 text-white border-blue-600 shadow-md' 
                                    : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}"
                                role="tab">
                            {{ ucfirst($tab) }}
                        </button>
                    @endforeach
                    <input type="hidden" name="type" id="report-type" value="{{ $reportType }}">
                    <input type="hidden" name="compare" id="compare-field" value="{{ request('compare', '0') }}">
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-end">
                    <div class="w-full sm:w-auto flex-1">
                        <label for="start_date" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" 
                               value="{{ old('start_date', (!empty($profit_loss) ? $profit_loss['start'] : now()->subMonths(6)->startOfMonth()->toDateString())) }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="w-full sm:w-auto flex-1">
                        <label for="end_date" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" 
                               value="{{ old('end_date', (!empty($profit_loss) ? $profit_loss['end'] : now()->endOfMonth()->toDateString())) }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div id="metrics-section" class="{{ $reportType == 'custom' ? '' : 'hidden' }} flex-1 w-full sm:w-auto">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Include</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach (['eggs', 'sales', 'expenses', 'payrolls', 'transactions', 'inventory'] as $metric)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="{{ $metric }}" {{ in_array($metric, old('metrics', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                    <span class="ml-1.5 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($metric) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900">
                        Update Report
                    </button>
                </div>
            </form>
        </div>

        @php
            $weekly = $data['weekly'] ?? collect();
            $monthly = $data['monthly'] ?? collect();
            $profitability = $data['profitability'] ?? collect();
            $forecast = $data['forecast'] ?? [];
            $efficiencyData = $data['efficiency'] ?? [];
        @endphp

        <div class="p-6 min-h-[500px]">
            
            <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Weekly Egg Report</h3>
            
            <div class="mb-8 h-80 w-full bg-gray-50 dark:bg-gray-700/20 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                <canvas id="weekly-chart"></canvas>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Time Period</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Daily Avg</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($weekly as $row)
                            @php
                                // Calculate start and end of the week
                                $dt = new \Carbon\Carbon();
                                $dt->setISODate($row->year, $row->week); 
                                $startOfWeek = $dt->startOfWeek()->format('d M');
                                $endOfWeek = $dt->copy()->endOfWeek()->format('d M');
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            Week {{ $row->week }} <span class="font-normal text-gray-500 text-xs">({{ $row->year }})</span>
                                        </span>
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-0.5">
                                            {{ $startOfWeek }} - {{ $endOfWeek }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">
                                    {{ number_format($row->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format($row->total / 7, 1) }}
                                </td>
                            </tr>
                        @empty
                                <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No data available for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
                <div class="mb-8 h-80 w-full bg-gray-50 dark:bg-gray-700/20 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                    <canvas id="monthly-chart"></canvas>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Crates</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($monthly as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $row->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ date("F", mktime(0, 0, 0, $row->month_num, 10)) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">{{ number_format($row->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No data available.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="efficiency-panel" class="tab-panel {{ $reportType === 'efficiency' ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50">
                        <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">FCR (Conversion)</div>
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ $efficiencyData['fcr'] ?? '0.00' }}</div>
                        <div class="text-xs text-blue-500/80 dark:text-blue-400/70 mt-1">Kg Feed / Crate</div>
                    </div>
                    <div class="p-5 bg-orange-50 dark:bg-orange-900/20 rounded-xl border border-orange-100 dark:border-orange-800/50">
                        <div class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wider">Feed Consumed</div>
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($efficiencyData['total_feed'] ?? 0) }}</div>
                        <div class="text-xs text-orange-500/80 dark:text-orange-400/70 mt-1">Kilograms</div>
                    </div>
                    <div class="p-5 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-100 dark:border-purple-800/50">
                        <div class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Vet. Costs</div>
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">₵ {{ number_format($efficiencyData['medicine_cost'] ?? 0, 0) }}</div>
                        <div class="text-xs text-purple-500/80 dark:text-purple-400/70 mt-1">Total spend</div>
                    </div>
                    <div class="p-5 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-800/50">
                        <div class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Output</div>
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ number_format($efficiencyData['total_crates'] ?? 0) }}</div>
                        <div class="text-xs text-teal-500/80 dark:text-teal-400/70 mt-1">Crates produced</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Mortality Trend</h4>
                        <div class="h-64"><canvas id="mortality-chart"></canvas></div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Expense Breakdown</h4>
                        <div class="h-64"><canvas id="expense-pie-chart"></canvas></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Egg Sizes (Quality)</h4>
                        <div class="h-64 flex justify-center"><canvas id="grade-pie-chart"></canvas></div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <h4 class="text-base font-semibold text-gray-700 dark:text-gray-200 mb-4">Top 5 Customers</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700">
                                        <th class="text-left py-2 font-medium text-gray-500">Customer</th>
                                        <th class="text-right py-2 font-medium text-gray-500">Spent</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($efficiencyData['top_customers'] ?? [] as $customer)
                                        <tr class="group">
                                            <td class="py-3 font-medium text-gray-800 dark:text-gray-200 group-hover:text-blue-500 transition-colors">{{ $customer['name'] }}</td>
                                            <td class="py-3 text-right font-bold text-gray-800 dark:text-gray-200">₵ {{ number_format($customer['total'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="py-6 text-center text-gray-500">No sales data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="payments-panel" class="tab-panel {{ $reportType === 'payments' ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @forelse($data['payments']['breakdown'] ?? [] as $method)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 
                            {{ $method->payment_method == 'cash' ? 'border-green-500' : ($method->payment_method == 'mobile_money' ? 'border-blue-500' : 'border-purple-500') }}">
                            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                                {{ str_replace('_', ' ', $method->payment_method) }}
                            </div>
                            <div class="text-2xl font-bold mt-1">₵ {{ number_format($method->total, 2) }}</div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-4 text-gray-500">No payment breakdown available.</div>
                    @endforelse
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-4 uppercase text-xs">Revenue Source Distribution</h4>
                        <div class="h-64"><canvas id="method-doughnut-chart"></canvas></div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="p-4 text-left">Date</th>
                                    <th class="p-4 text-left">Customer</th>
                                    <th class="p-4 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                @forelse($data['payments']['list'] ?? [] as $pay)
                                    <tr>
                                        <td class="p-4 text-gray-500">{{ $pay->payment_date->format('d M') }}</td>
                                        <td class="p-4 font-medium">{{ $pay->customer->name ?? 'Unknown Customer' }}</td>
                                        <td class="p-4 text-right font-bold text-green-600">₵{{ number_format($pay->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-4 text-center text-gray-400">No recent payments.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 mt-8">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y dark:divide-gray-700">
                            @forelse($data['payments']['detailed'] ?? [] as $pay)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $pay->payment_date->format('d M, Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $pay->customer->name ?? 'Guest' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-[10px] font-bold uppercase rounded bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                                            {{ str_replace('_', ' ', $pay->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-green-600">₵ {{ number_format($pay->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-10 text-center text-gray-400 italic">No payments found for this range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="custom-panel" class="tab-panel {{ $reportType === 'custom' ? '' : 'hidden' }}">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Detailed Metrics</h3>
                
                @if(!empty($data['eggs']) && count($data['eggs']) > 0)
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-gray-700 dark:text-gray-200">Eggs Produced</h4>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded dark:bg-blue-900 dark:text-blue-200">{{ count($data['eggs']) }} records</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Date</th>
                                        <th class="px-4 py-2 text-right text-gray-500 dark:text-gray-400">Crates</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($data['eggs'] as $e)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($e->date_laid)->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-white">{{ $e->crates }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(!empty($data['sales']) && count($data['sales']) > 0)
                    <div class="mb-8">
                        <h4 class="font-bold text-gray-700 dark:text-gray-200 mb-2">Sales Records</h4>
                        <div class="max-h-80 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Date</th>
                                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Customer</th>
                                        <th class="px-4 py-2 text-right text-gray-500 dark:text-gray-400">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($data['sales'] as $s)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::parse($s->sale_date)->format('d/m/Y') }}</td>
                                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $s->customer->name ?? '-' }}</td>
                                            <td class="px-4 py-2 text-right font-medium text-green-600 dark:text-green-400">₵ {{ number_format($s->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                
                @if(empty($data['eggs']) && empty($data['sales']) && empty($data['expenses']))
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">Select metrics above to view detailed data tables.</p>
                    </div>
                @endif
            </div>

            <div id="forecast-panel" class="tab-panel {{ $reportType === 'forecast' ? '' : 'hidden' }}">
                <h3 class="text-xl font-semibold mb-6 dark:text-white">Financial Projections (Next 30 Days)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div class="p-6 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800/50 shadow-sm">
                        <div class="text-xs text-green-600 dark:text-green-400 uppercase font-bold tracking-wider">Est. Income</div>
                        <div class="text-3xl font-extrabold text-green-700 dark:text-green-300 mt-2">₵ {{ number_format($forecast['forecasted_income'] ?? 0, 2) }}</div>
                        <div class="text-xs text-green-600/70 dark:text-green-400/60 mt-1">+5% Trend</div>
                    </div>
                    <div class="p-6 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800/50 shadow-sm">
                        <div class="text-xs text-red-600 dark:text-red-400 uppercase font-bold tracking-wider">Est. Expenses</div>
                        <div class="text-3xl font-extrabold text-red-700 dark:text-red-300 mt-2">₵ {{ number_format($forecast['forecasted_expenses'] ?? 0, 2) }}</div>
                        <div class="text-xs text-red-600/70 dark:text-red-400/60 mt-1">+3% Trend</div>
                    </div>
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800/50 shadow-sm">
                        <div class="text-xs text-blue-600 dark:text-blue-400 uppercase font-bold tracking-wider">Est. Net Profit</div>
                        <div class="text-3xl font-extrabold text-blue-700 dark:text-blue-300 mt-2">₵ {{ number_format($forecast['forecasted_profit'] ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Calculations based on 6-month historical averages adjusted for inflation.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="advanced-export-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-gray-900/50 backdrop-blur-sm transition-opacity">
    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-8 shadow-2xl border border-gray-100 dark:border-gray-700 transform scale-100 transition-transform">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Export Configuration</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Customize data columns and format for your download.</p>
        
        <form id="advanced-export-form" method="GET" action="" class="space-y-6" target="_blank">
            <input type="hidden" name="type" value="{{ $reportType }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Columns</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                        <input type="checkbox" name="columns[]" value="date" checked class="rounded text-blue-600 focus:ring-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Date</span>
                    </label>
                    <label class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                        <input type="checkbox" name="columns[]" value="amount" checked class="rounded text-blue-600 focus:ring-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Amount/Qty</span>
                    </label>
                    <label class="flex items-center p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                        <input type="checkbox" name="columns[]" value="description" class="rounded text-blue-600 focus:ring-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Details</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="advanced-export-cancel" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Download</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
{{-- Use local Chart.js asset --}}
<script src="{{ asset('js/chart.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Check if Chart.js loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded. Please ensure public/js/chart.js exists.');
        return;
    }

    // Tab Switching Logic
    const tabs = document.querySelectorAll('.tab-btn');
    const typeInput = document.getElementById('report-type');
    const filterForm = document.getElementById('report-filter-form');
    const compareToggle = document.getElementById('compare-toggle');
    const compareField = document.getElementById('compare-field');

    // Handle compare toggle
    if (compareToggle && compareField) {
        compareToggle.addEventListener('change', (e) => {
            compareField.value = e.target.checked ? '1' : '0';
        });
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab');
            
            if(target === typeInput.value) return;

            typeInput.value = target;
            filterForm.submit();
        });
    });

    // --- Chart Data Preparation ---
    const efficiencyData = @json($efficiencyData);
    const weeklyData = @json($weekly);
    const monthlyData = @json($monthly);
    const reportType = "{{ $reportType }}";
    const paymentsData = @json($data['payments'] ?? []);

    // Helper: Convert YYYY-MM-DD to DD/MM/YYYY
    const formatDate = (isoString) => {
        if (!isoString) return '';
        const d = new Date(isoString);
        return d.toLocaleDateString('en-GB');
    };

    // Global Chart Defaults for Dark Mode
    const isDark = document.documentElement.classList.contains('dark');
    Chart.defaults.color = isDark ? '#9ca3af' : '#4b5563';
    Chart.defaults.borderColor = isDark ? '#374151' : '#e5e7eb';

    // Chart.js Helper
    const createChart = (id, type, labels, data, label, color = 'blue') => {
        const ctx = document.getElementById(id);
        if(!ctx) return null;
        
        const colors = {
            blue: 'rgba(59, 130, 246, 0.5)',
            red: 'rgba(239, 68, 68, 0.5)',
            green: 'rgba(16, 185, 129, 0.5)',
            purple: 'rgba(139, 92, 246, 0.5)',
            orange: 'rgba(251, 146, 60, 0.5)'
        };

        const borderColors = {
            blue: '#3b82f6',
            red: '#ef4444',
            green: '#10b981',
            purple: '#8b5cf6',
            orange: '#fb923c'
        };

        return new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: colors[color],
                    borderColor: borderColors[color],
                    borderWidth: 2,
                    tension: 0.3,
                    fill: type === 'line',
                    pointBackgroundColor: borderColors[color],
                    pointRadius: 4
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4] }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    };

    // Helper to get date range from week number
    const getWeekRange = (year, week) => {
        const d = new Date(year, 0, 1 + (week - 1) * 7);
        const dayOfWeek = d.getDay();
        const ISOweekStart = new Date(d);
        if (dayOfWeek <= 4) 
            ISOweekStart.setDate(d.getDate() - d.getDay() + 1);
        else 
            ISOweekStart.setDate(d.getDate() + 8 - d.getDay());
        
        const end = new Date(ISOweekStart);
        end.setDate(end.getDate() + 6);
        
        const fmt = date => date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
        return `W${week}: ${fmt(ISOweekStart)} - ${fmt(end)}`;
    };

    // WEEKLY REPORT
    if (reportType === 'weekly' && weeklyData.length) {
        const labels = weeklyData.map(d => `W${d.week}`);
        const ctx = document.getElementById('weekly-chart');
        
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Crates',
                        data: weeklyData.map(d => d.total),
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const row = weeklyData[index];
                                    return getWeekRange(row.year, row.week);
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }
    
    // MONTHLY REPORT
    if (reportType === 'monthly' && monthlyData.length) {
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const labels = monthlyData.map(d => monthNames[d.month_num - 1] + ' ' + d.year);
        const data = monthlyData.map(d => d.total);
        createChart('monthly-chart', 'bar', labels, data, 'Crates', 'green');
    }

    // EFFICIENCY REPORT
    if (reportType === 'efficiency') {
        // Mortality Trend
        if (efficiencyData.mortality_trend) {
            const mLabels = efficiencyData.mortality_trend.map(d => formatDate(d.date));
            const mData = efficiencyData.mortality_trend.map(d => d.value);
            createChart('mortality-chart', 'line', mLabels, mData, 'Daily Mortality', 'red');
        }

        // Expense Breakdown (Pie)
        if (efficiencyData.expense_breakdown) {
            const eCtx = document.getElementById('expense-pie-chart');
            if (eCtx) {
                new Chart(eCtx, {
                    type: 'doughnut',
                    data: {
                        labels: efficiencyData.expense_breakdown.map(d => d.category),
                        datasets: [{
                            data: efficiencyData.expense_breakdown.map(d => d.total),
                            backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6'],
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { position: 'right' }
                        }
                    }
                });
            }
        }

        // Egg Grades (Pie)
        if (efficiencyData.egg_grades) {
            const gCtx = document.getElementById('grade-pie-chart');
            if (gCtx) {
                const gLabels = efficiencyData.egg_grades.map(d => (d.egg_size || 'Unsorted').charAt(0).toUpperCase() + (d.egg_size || 'Unsorted').slice(1));
                new Chart(gCtx, {
                    type: 'pie',
                    data: {
                        labels: gLabels,
                        datasets: [{
                            data: efficiencyData.egg_grades.map(d => d.total),
                            backgroundColor: ['#EC4899', '#6366F1', '#14B8A6', '#F59E0B'],
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { position: 'right' }
                        }
                    }
                });
            }
        }
    }

    // PAYMENTS REPORT
    if (reportType === 'payments') {
        const paymentCtx = document.getElementById('method-doughnut-chart');

        if (paymentCtx && paymentsData.chartData && paymentsData.chartData.length > 0) {
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: paymentsData.chartLabels,
                    datasets: [{
                        data: paymentsData.chartData,
                        backgroundColor: ['#10B981', '#3B82F6', '#8B5CF6', '#F59E0B'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '70%'
                }
            });
        }
    }

    // Modal Logic
    const modal = document.getElementById('advanced-export-modal');
    const openBtn = document.getElementById('export-advanced');
    const closeBtn = document.getElementById('advanced-export-cancel');
    const dropdown = document.getElementById('export-dropdown');
    const exportMainBtn = document.getElementById('export-btn');

    // Dropdown toggle
    if (exportMainBtn) {
        exportMainBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (dropdown && !dropdown.contains(e.target) && exportMainBtn && !exportMainBtn.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    if(openBtn) {
        openBtn.addEventListener('click', () => {
            dropdown.classList.add('hidden');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }
    
    if(closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    }

    // Export format selection
    document.querySelectorAll('.export-option').forEach(btn => {
        btn.addEventListener('click', () => {
            const format = btn.getAttribute('data-format');
            const url = new URL(window.location.href);
            url.pathname = '/reports/export';
            url.searchParams.set('format', format);
            url.searchParams.set('type', reportType);
            
            window.open(url.toString(), '_blank');
            dropdown.classList.add('hidden');
        });
    });

    // Preset buttons logic (Updates input dates)
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const days = parseInt(btn.dataset.days);
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - days);
            
            const fmt = d => d.toISOString().split('T')[0];
            document.getElementById('start_date').value = fmt(start);
            document.getElementById('end_date').value = fmt(end);
        });
    });

    // Show/hide custom metrics based on tab selection
    const metricsSection = document.getElementById('metrics-section');
    if (metricsSection) {
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-tab');
                if (target === 'custom') {
                    metricsSection.classList.remove('hidden');
                } else {
                    metricsSection.classList.add('hidden');
                }
            });
        });
    }
});
</script>
@endpush
@endsection