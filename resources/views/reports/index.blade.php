@extends('layouts.app')

@section('content')
<style>
    /* Utility to hide scrollbar while allowing scrolling */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300 overflow-x-hidden">
    
    {{-- Toast Notification --}}
    <div id="toast" class="fixed right-6 top-6 z-50 invisible pointer-events-none transition-all duration-300 ease-out opacity-0 transform translate-y-4">
        <div id="toastInner" class="max-w-sm rounded-xl p-4 shadow-xl bg-gray-800 dark:bg-gray-700 text-white flex items-center space-x-3 border border-gray-700 dark:border-gray-600">
            <svg class="h-5 w-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span id="toastMessage" class="font-medium text-sm truncate"></span>
        </div>
    </div>

    {{-- Header & Filters --}}
    <section class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div class="flex-1 w-full">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Farm Analytics</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-2">Deep dive into trends, financials, and efficiency.</p>

            {{-- Mobile scrollable filter buttons --}}
            <div class="mt-6 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 -mx-4 px-4 sm:mx-0 sm:px-0" id="preset-buttons-container">
                @foreach([7 => '7d', 30 => '30d', 90 => '3M', 180 => '6M', 365 => 'YTD'] as $days => $label)
                    <button type="button" class="preset-btn flex-shrink-0 inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 shadow-sm hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-2 focus:ring-blue-500 transition-all duration-200" data-days="{{ $days }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Export Button (Fixed UX) --}}
        <div class="w-full sm:w-auto flex-shrink-0 mt-4 lg:mt-0 relative">
            <button id="export-btn" class="w-full lg:w-auto justify-center inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-md transition-all duration-200 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Report
                <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-full lg:w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 border border-gray-200 dark:border-gray-700 overflow-hidden transform origin-top-right transition-all">
                <button data-format="pdf" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg> PDF Report
                </button>
                <button data-format="excel" class="export-option w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg> Excel Data
                </button>
            </div>
        </div>
    </section>

    {{-- Main KPI Cards (Added hover translate) --}}
    <section id="kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $profit_loss = $data['profit_loss'] ?? [];
            $totals = $data['totals'] ?? [];
            $efficiencyData = $data['efficiency'] ?? [];
        @endphp

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md hover:-translate-y-1 transform transition-all duration-300">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Income</div>
            <div class="text-3xl font-extrabold mt-2 text-gray-900 dark:text-white truncate">₵ {{ number_format($profit_loss['total_income'] ?? ($totals['income'] ?? 0), 2) }}</div>
            <div class="absolute right-4 top-4 p-2 bg-green-50 dark:bg-green-900/30 rounded-full">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md hover:-translate-y-1 transform transition-all duration-300">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expenses</div>
            <div class="text-3xl font-extrabold mt-2 text-gray-900 dark:text-white truncate">₵ {{ number_format(($profit_loss['total_expenses'] ?? 0) + ($profit_loss['total_payroll'] ?? 0), 2) }}</div>
            <div class="absolute right-4 top-4 p-2 bg-red-50 dark:bg-red-900/30 rounded-full">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md hover:-translate-y-1 transform transition-all duration-300">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</div>
            <div class="text-3xl font-extrabold mt-2 truncate {{ (($profit_loss['profit_loss'] ?? 0) >= 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                ₵ {{ number_format($profit_loss['profit_loss'] ?? ($totals['profit'] ?? 0), 2) }}
            </div>
            <div class="absolute right-4 top-4 p-2 bg-blue-50 dark:bg-blue-900/30 rounded-full">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:shadow-md hover:-translate-y-1 transform transition-all duration-300">
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avg Production</div>
            <div class="text-3xl font-extrabold mt-2 text-gray-900 dark:text-white truncate">{{ number_format($data['avg_crates_per_day'] ?? 0, 1) }} <span class="text-sm font-normal text-gray-500">cr/day</span></div>
            <div class="absolute right-4 top-4 p-2 bg-amber-50 dark:bg-amber-900/30 rounded-full">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
        </div>
    </section>

    {{-- Executive Intelligence --}}
    <section>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-300 transition-colors cursor-default" title="Feed cost required to generate ₵100 in revenue">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">Economic FCR <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg></div>
                <div class="text-2xl font-black text-blue-600 mt-2 truncate">₵{{ number_format($data['advanced_metrics']['economic_fcr'] ?? 0, 2) }}</div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-300 transition-colors">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Production Gap</div>
                <div class="text-2xl font-black {{ ($data['advanced_metrics']['production_gap'] ?? 0) > 15 ? 'text-red-500' : 'text-green-500' }} mt-2">
                    {{ $data['advanced_metrics']['production_gap'] ?? 0 }}%
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-300 transition-colors cursor-default" title="Current value of unsold inventory">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">Dead Money <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg></div>
                <div class="text-2xl font-black text-orange-600 mt-2 truncate">₵{{ number_format($data['advanced_metrics']['dead_money'] ?? 0, 0) }}</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-300 transition-colors cursor-default" title="Revenue generated per ₵1.00 paid in wages">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">Labor ROI <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg></div>
                <div class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $data['advanced_metrics']['labor_efficiency'] ?? '0.00' }}x</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 col-span-2 lg:col-span-1 hover:border-blue-300 transition-colors">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock Age</div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $data['advanced_metrics']['stock_aging_days'] ?? 0 }}d</span>
                    <span class="text-[10px] px-2 py-1 rounded-full font-bold uppercase {{ ($data['advanced_metrics']['spoilage_risk'] ?? '') === 'High' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">{{ $data['advanced_metrics']['spoilage_risk'] ?? 'Low' }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- EGG CRATES INTELLIGENCE --}}
    <section class="mt-12">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-2xl flex items-center justify-center text-3xl shadow-inner">🥚</div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Egg Crates Intelligence</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Smart analysis of your crate production over the selected period</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Total Crates</div>
                <div class="text-4xl font-black text-gray-900 dark:text-white mt-3">{{ number_format($data['egg_intelligence']['total_crates'] ?? 0) }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ number_format($data['egg_intelligence']['total_eggs'] ?? 0) }} eggs</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Avg Daily Crates</div>
                <div class="text-4xl font-black text-gray-900 dark:text-white mt-3">{{ $data['egg_intelligence']['avg_daily_crates'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 mt-1">per active day</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Growth vs Prev Period</div>
                @php
                    $gr = $data['egg_intelligence']['growth_rate'] ?? 0;
                    $growthClass = $gr >= 0 ? 'text-emerald-600' : 'text-red-600';
                @endphp
                <div class="text-4xl font-black {{ $growthClass }} mt-3 flex items-center">
                    {{ $gr > 0 ? '+' : '' }}{{ $gr }}%
                    <svg class="w-6 h-6 ml-2 {{ $gr >= 0 ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </div>
                <div class="text-xs text-gray-500 mt-1">vs previous {{ (int) round($start->diffInDays($end) + 1) }} days</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Consistency Score</div>
                <div class="text-4xl font-black text-gray-900 dark:text-white mt-3">{{ $data['egg_intelligence']['consistency_score'] ?? 0 }}<span class="text-base font-normal text-gray-400">/100</span></div>
                <div class="text-xs text-gray-500 mt-1">Higher = less variance</div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950 dark:to-gray-800 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 text-sm font-bold uppercase tracking-wide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg> Peak Day
                </div>
                <div class="text-3xl font-black mt-3 text-gray-900 dark:text-white">{{ $data['egg_intelligence']['peak_day'] }}</div>
                <div class="text-xl font-semibold text-emerald-600 mt-1">{{ number_format($data['egg_intelligence']['peak_crates'] ?? 0) }} crates</div>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-white dark:from-red-950 dark:to-gray-800 border border-red-200 dark:border-red-800/50 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-2 text-red-700 dark:text-red-400 text-sm font-bold uppercase tracking-wide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg> Lowest Day
                </div>
                <div class="text-3xl font-black mt-3 text-gray-900 dark:text-white">{{ $data['egg_intelligence']['lowest_day'] }}</div>
                <div class="text-xl font-semibold text-red-600 mt-1">{{ number_format($data['egg_intelligence']['lowest_crates'] ?? 0) }} crates</div>
            </div>
        </div>

        <div class="mt-8 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-950/40 dark:to-yellow-900/20 border border-amber-200 dark:border-amber-800/50 rounded-3xl p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl bg-white dark:bg-gray-800 rounded-full p-2 shadow-sm">🧠</span>
                <h4 class="font-extrabold text-xl text-amber-900 dark:text-amber-200 tracking-tight">AI Insights</h4>
            </div>
            <div class="text-base sm:text-lg leading-relaxed text-amber-800 dark:text-amber-100/90 font-medium">
                {!! $data['egg_intelligence']['insight'] ?? 'No data yet.' !!}
            </div>
        </div>
    </section>

    {{-- Main Report Body --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mt-12">
        
        {{-- Sticky Filter & Tab Form (UX Fix) --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/95 dark:bg-gray-800/95 backdrop-blur sticky top-0 z-30 shadow-sm">
            <form id="report-filter-form" method="GET" action="{{ route('reports.index') }}">
                @csrf
                <div class="flex flex-wrap gap-2 mb-4 sm:mb-6">
                    @foreach (['trends', 'comparisons', 'weekly', 'monthly', 'payments', 'efficiency', 'forecast'] as $tab)
                        <button type="button" data-tab="{{ $tab }}"
                                class="tab-btn px-4 py-2 rounded-lg text-sm font-bold transition-all duration-200 border 
                                {{ $reportType === $tab ? 'bg-blue-600 text-white border-blue-600 shadow-md transform scale-105' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                            {{ ucfirst($tab) }}
                        </button>
                    @endforeach
                    <input type="hidden" name="type" id="report-type" value="{{ $reportType }}">
                    <input type="hidden" name="compare" id="compare-field" value="{{ request('compare', '0') }}">
                </div>

                <div class="flex flex-col sm:flex-row gap-4 items-end bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-inner">
                    <div class="w-full sm:w-auto flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Custom Date Range</label>
                        <div class="flex items-center gap-2">
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $data['profit_loss']['start'] ?? now()->subDays(30)->toDateString()) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm transition-shadow">
                            <span class="text-gray-400 font-medium">to</span>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $data['profit_loss']['end'] ?? now()->toDateString()) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm transition-shadow">
                        </div>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gray-900 hover:bg-black dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded-lg font-bold text-sm transition-colors shadow-md flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Update View
                    </button>
                </div>
            </form>
        </div>

        {{-- Panel Content --}}
        <div class="p-4 sm:p-6 min-h-[500px]">
            
            {{-- TAB: TRENDS --}}
            <div id="trends-panel" class="tab-panel {{ $reportType === 'trends' ? '' : 'hidden' }}">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center"><span class="w-2 h-6 bg-blue-500 rounded-full mr-3"></span>Performance Trends</h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach([
                        ['id' => 'eggTrend', 'title' => 'Egg Production', 'color' => 'blue'],
                        ['id' => 'feedTrend', 'title' => 'Feed Consumption', 'color' => 'yellow'],
                        ['id' => 'salesTrend', 'title' => 'Sales Volume', 'color' => 'green'],
                        ['id' => 'incomeTrend', 'title' => 'Income Flow', 'color' => 'blue'] 
                    ] as $chart)
                    <div class="chart-container bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm transition-shadow hover:shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-sm sm:text-base text-gray-700 dark:text-gray-200">{{ $chart['title'] }}</h4>
                            {{-- UX Fix: Data-target attribute maps to canvas id --}}
                            <select class="chart-type-selector text-xs font-medium rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:ring-blue-500 shadow-sm cursor-pointer" data-target="{{ $chart['id'] }}" data-color="{{ $chart['color'] }}">
                                <option value="line">Line Chart</option>
                                <option value="bar">Bar Chart</option>
                            </select>
                        </div>
                        <div class="h-64 sm:h-80 relative w-full">
                            <canvas id="{{ $chart['id'] }}"></canvas>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TAB: COMPARISONS --}}
            <div id="comparisons-panel" class="tab-panel {{ $reportType === 'comparisons' ? '' : 'hidden' }}">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center"><span class="w-2 h-6 bg-purple-500 rounded-full mr-3"></span>Strategic Comparisons</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-lg text-gray-700 dark:text-white">Sales: Eggs vs Birds</h4>
                            {{-- Wait to render comparisons dynamically --}}
                        </div>
                        <div class="h-72 sm:h-96 relative w-full"><canvas id="salesComparison"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- TAB: PAYMENTS --}}
            <div id="payments-panel" class="tab-panel {{ $reportType === 'payments' ? '' : 'hidden' }}">
                <div class="mb-8 p-6 dark:bg-gray-700/30 bg-gradient-to-r from-indigo-50 to-white dark:from-indigo-950/50 dark:to-gray-800 rounded-2xl border border-indigo-100 dark:border-indigo-800/50 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div>
                        <h4 class="text-sm font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-1 flex items-center"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> Total Transaction Volume</h4>
                        <div class="text-4xl font-black text-gray-900 dark:text-white mt-2 tracking-tight">
                            ₵ {{ number_format($data['totalTransactions'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm transition-shadow hover:shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-sm sm:text-base text-gray-800 dark:text-white">Transaction History</h4>
                            <select class="chart-type-selector text-xs font-medium rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:ring-blue-500 cursor-pointer" data-target="transactionTrend" data-color="purple">
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                        <div class="h-64 sm:h-80 relative w-full"><canvas id="transactionTrend"></canvas></div>
                    </div>

                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm transition-shadow hover:shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-sm sm:text-base text-gray-800 dark:text-white">Invoice Status</h4>
                            <select class="chart-type-selector text-xs font-medium rounded-lg border-gray-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:ring-blue-500 cursor-pointer" data-target="invoiceStatus" data-is-pie="true">
                                <option value="bar">Bar</option>
                                <option value="pie">Pie</option>
                            </select>
                        </div>
                        <div class="h-64 sm:h-80 relative w-full"><canvas id="invoiceStatus"></canvas></div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-700/30 rounded-xl p-4 sm:p-6 border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm">
                        <h4 class="font-bold text-sm sm:text-base mb-4 text-gray-800 dark:text-white">Payment Methods</h4>
                        <div class="h-64 sm:h-80 relative w-full"><canvas id="method-doughnut-chart"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- TAB: WEEKLY --}}
            <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
                <h3 class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center"><span class="w-2 h-6 bg-teal-500 rounded-full mr-3"></span>Weekly Production</h3>
                <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm">
                   <div class="h-64 sm:h-96 relative w-full"><canvas id="weekly-chart"></canvas></div>
                </div>
            </div>

            {{-- TAB: MONTHLY --}}
            <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
                 <h3 class="text-xl sm:text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center"><span class="w-2 h-6 bg-purple-500 rounded-full mr-3"></span>Monthly Overview</h3>
                 <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-700 w-full min-w-0 shadow-sm">
                    <div class="h-64 sm:h-96 relative w-full"><canvas id="monthly-chart"></canvas></div>
                 </div>
            </div>
            
            {{-- TAB: EFFICIENCY --}}
            <div id="efficiency-panel" class="tab-panel {{ $reportType === 'efficiency' ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/50 hover:shadow-md transition-shadow">
                        <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider flex justify-between">FCR (Conversion) <svg class="w-4 h-4 opacity-50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg></div>
                        <div class="text-4xl font-black text-gray-800 dark:text-gray-100 mt-3">{{ $efficiencyData['fcr'] ?? '0.00' }}</div>
                    </div>
                    <div class="p-6 bg-orange-50 dark:bg-orange-900/20 rounded-2xl border border-orange-100 dark:border-orange-800/50 hover:shadow-md transition-shadow">
                        <div class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wider">Feed Consumed</div>
                        <div class="text-4xl font-black text-gray-800 dark:text-gray-100 mt-3">{{ number_format($efficiencyData['total_feed'] ?? 0) }}<span class="text-lg font-normal text-gray-500 ml-1">kg</span></div>
                    </div>
                    <div class="p-6 bg-purple-50 dark:bg-purple-900/20 rounded-2xl border border-purple-100 dark:border-purple-800/50 hover:shadow-md transition-shadow">
                        <div class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Vet. Costs</div>
                        <div class="text-4xl font-black text-gray-800 dark:text-gray-100 mt-3"><span class="text-xl">₵</span>{{ number_format($efficiencyData['medicine_cost'] ?? 0, 0) }}</div>
                    </div>
                    <div class="p-6 bg-teal-50 dark:bg-teal-900/20 rounded-2xl border border-teal-100 dark:border-teal-800/50 hover:shadow-md transition-shadow">
                        <div class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">Total Output</div>
                        <div class="text-4xl font-black text-gray-800 dark:text-gray-100 mt-3">{{ number_format($efficiencyData['total_crates'] ?? 0) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 w-full min-w-0">
                          <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Mortality Trend</h4>
                          <div class="h-64 w-full relative"><canvas id="mortality-chart"></canvas></div>
                      </div>
                      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 w-full min-w-0">
                          <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Expense Breakdown</h4>
                          <div class="h-64 w-full relative"><canvas id="expense-pie-chart"></canvas></div>
                      </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
    // Store chart instances to update them dynamically
    window.farmCharts = {};

    const COLORS = {
        blue: '#3B82F6',   // Production
        green: '#10B981',  // Money/Sales
        red: '#EF4444',    // Expenses/Mortality
        yellow: '#F59E0B', // Feed
        purple: '#8B5CF6', // Monthly
        teal: '#14B8A6',   // Output
        gray: '#9ca3af'
    };

    function normalizeSeries(raw) {
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
                    labels: raw.map(r => r.date ?? r.label ?? r.week ?? r.category ?? ''),
                    values: raw.map(r => Number(r.value ?? r.total ?? r.amount ?? r.crates ?? 0))
                };
            }
            return { labels: raw.map((_, i) => i + 1), values: raw.map(v => Number(v)) };
        }
        return { labels: [], values: [] };
    }

    const RAW_DATA = {
        eggTrend: @json($data['charts']['eggTrend'] ?? []),
        feedTrend: @json($data['charts']['feedTrend'] ?? []),
        salesTrend: @json($data['charts']['salesTrend'] ?? []),
        incomeTrend: @json($data['charts']['incomeTrend'] ?? []),
        salesComparison: @json($data['charts']['salesComparison'] ?? []),
        invoiceStatuses: @json($data['charts']['invoiceStatuses'] ?? []),
        transactionTrend: @json($data['charts']['transactionTrend'] ?? []),
        mortalityTrend: @json($data['efficiency']['mortality_trend'] ?? []),
        expenseBreakdown: @json($data['efficiency']['expense_breakdown'] ?? []),
        weekly: @json($data['weekly'] ?? []),
        monthly: @json($data['monthly'] ?? []),
        payments: @json($data['payments'] ?? [])
    };

    // Upgraded chart creation to return and save the instance
    function buildChart(canvasId, type, data, colorName, isPie = false) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        if (window.farmCharts[canvasId]) {
            window.farmCharts[canvasId].destroy();
        }

        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#D1D5DB' : '#4B5563';
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
        const hexColor = COLORS[colorName] || COLORS.blue;

        let bgColors = type === 'line' ? hexColor + '20' : hexColor;
        let borderColors = hexColor;

        if (isPie || type === 'pie' || type === 'doughnut') {
            bgColors = [COLORS.blue, COLORS.green, COLORS.yellow, COLORS.red, COLORS.purple, COLORS.teal];
            borderColors = isDark ? '#1F2937' : '#FFFFFF';
        } else if (type === 'bar' && data.values && data.values.length > 1) {
             // Optional: if it's a bar chart, we can keep solid color, but round the borders
             bgColors = hexColor + 'E6';
        }

        const config = {
            type: type,
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Value',
                    data: data.values,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: isPie ? 2 : 2,
                    fill: type === 'line',
                    tension: 0.3,
                    borderRadius: type === 'bar' ? 4 : 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: isPie || type === 'pie' || type === 'doughnut', position: 'right', labels: {color: textColor} },
                    tooltip: {
                        backgroundColor: isDark ? 'rgba(17, 24, 39, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                        titleColor: isDark ? '#F3F4F6' : '#111827',
                        bodyColor: isDark ? '#D1D5DB' : '#4B5563',
                        borderColor: isDark ? '#374151' : '#E5E7EB',
                        borderWidth: 1,
                        padding: 10,
                        boxPadding: 4
                    }
                },
                scales: (isPie || type === 'pie' || type === 'doughnut') ? {} : {
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: {family: "'Inter', sans-serif"} } },
                    x: { grid: { display: false }, ticks: { color: textColor, font: {family: "'Inter', sans-serif"} } }
                }
            }
        };

        window.farmCharts[canvasId] = new Chart(canvas.getContext('2d'), config);
        return window.farmCharts[canvasId];
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initial Render
        buildChart('eggTrend', 'line', normalizeSeries(RAW_DATA.eggTrend), 'blue');
        buildChart('feedTrend', 'line', normalizeSeries(RAW_DATA.feedTrend), 'yellow');
        buildChart('salesTrend', 'line', normalizeSeries(RAW_DATA.salesTrend), 'green');
        buildChart('incomeTrend', 'line', normalizeSeries(RAW_DATA.incomeTrend), 'blue');
        buildChart('weekly-chart', 'bar', normalizeSeries(RAW_DATA.weekly), 'teal');
        buildChart('monthly-chart', 'bar', normalizeSeries(RAW_DATA.monthly), 'purple');
        buildChart('mortality-chart', 'line', normalizeSeries(RAW_DATA.mortalityTrend), 'red');
        buildChart('transactionTrend', 'line', normalizeSeries(RAW_DATA.transactionTrend), 'purple');

        // Invoice Status (Bar by default)
        buildChart('invoiceStatus', 'bar', normalizeSeries(RAW_DATA.invoiceStatuses), 'yellow');

        // Multi-line comparisons
        const sc = RAW_DATA.salesComparison;
        if(sc && sc.length && document.getElementById('salesComparison')) {
            window.farmCharts['salesComparison'] = new Chart(document.getElementById('salesComparison'), {
                type: 'line',
                data: {
                    labels: sc.map(r => r.date),
                    datasets: [
                        { label: 'Eggs', data: sc.map(r => r.egg_sales), borderColor: COLORS.green, backgroundColor: COLORS.green+'10', fill:true, tension: 0.4 },
                        { label: 'Birds', data: sc.map(r => r.bird_sales), borderColor: COLORS.blue, backgroundColor: COLORS.blue+'10', fill:true, tension: 0.4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true } } }
            });
        }

        // Doughnuts & Pies
        const expData = normalizeSeries(RAW_DATA.expenseBreakdown);
        if(document.getElementById('expense-pie-chart')) {
            buildChart('expense-pie-chart', 'doughnut', expData, 'blue', true);
        }

        const payData = normalizeSeries(RAW_DATA.payments ? RAW_DATA.payments.chartData : {});
        if (document.getElementById('method-doughnut-chart')) {
            buildChart('method-doughnut-chart', 'doughnut', payData, 'purple', true);
        }

        // --- UX FIX: Dynamic Chart Selectors ---
        document.querySelectorAll('.chart-type-selector').forEach(select => {
            select.addEventListener('change', function() {
                const targetId = this.dataset.target;
                const newType = this.value;
                const color = this.dataset.color || 'blue';
                const isPie = this.dataset.isPie === 'true' || newType === 'pie';
                
                // Get data source dynamically based on target ID
                let rawSource = RAW_DATA[targetId];
                if(targetId === 'invoiceStatus') rawSource = RAW_DATA.invoiceStatuses;

                buildChart(targetId, newType, normalizeSeries(rawSource), color, isPie);
            });
        });

        // Tab Switching Logic
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.dataset.tab;
                
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md', 'transform', 'scale-105');
                    b.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300', 'border-gray-200', 'dark:border-gray-600');
                });
                
                this.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300', 'border-gray-200', 'dark:border-gray-600');
                this.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-md', 'transform', 'scale-105');

                document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
                document.getElementById(target + '-panel').classList.remove('hidden');
                
                // Force resize to fix Chart.js canvas render issues on hidden tabs
                setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
            });
        });

        // --- UX FIX: Export Dropdown Logic ---
        const exportBtn = document.getElementById('export-btn');
        const exportDropdown = document.getElementById('export-dropdown');
        
        if(exportBtn && exportDropdown) {
            exportBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                exportDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                exportDropdown.classList.add('hidden');
            });

            document.querySelectorAll('.export-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const format = this.dataset.format;
                    const form = document.getElementById('report-filter-form');
                    
                    // Create temporary hidden input for format
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'format';
                    input.value = format;
                    form.appendChild(input);

                    // Create temporary hidden input to request export endpoint (if handled by standard form)
                    // Alternative: Modify form action
                    const originalAction = form.action;
                    form.action = "{{ route('reports.export') ?? '#' }}"; // Ensure you have this named route
                    form.submit();
                    
                    // Cleanup
                    form.action = originalAction;
                    input.remove();
                    exportDropdown.classList.add('hidden');
                });
            });
        }

        // --- UX FIX: Handle Preset Date Buttons ---
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        
        // Highlight active preset on load based on inputs
        function checkActivePreset() {
            if(!startInput || !endInput) return;
            const startD = new Date(startInput.value);
            const endD = new Date(endInput.value);
            const diffTime = Math.abs(endD - startD);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            document.querySelectorAll('.preset-btn').forEach(btn => {
                const btnDays = parseInt(btn.dataset.days);
                if(diffDays === btnDays || (diffDays >= btnDays-2 && diffDays <= btnDays+2)) { // tiny buffer
                    btn.classList.add('bg-blue-100', 'dark:bg-blue-900', 'border-blue-300', 'text-blue-800', 'dark:text-blue-200');
                    btn.classList.remove('bg-white', 'text-gray-700');
                } else {
                    btn.classList.remove('bg-blue-100', 'dark:bg-blue-900', 'border-blue-300', 'text-blue-800', 'dark:text-blue-200');
                }
            });
        }
        checkActivePreset();

        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const days = parseInt(this.dataset.days);
                const end = new Date();
                let start = new Date();
                
                if (days === 365) {
                    start = new Date(end.getFullYear(), 0, 1);
                } else {
                    start.setDate(start.getDate() - days);
                }

                const formatDate = (date) => date.toISOString().split('T')[0];
                startInput.value = formatDate(start);
                endInput.value = formatDate(end);
                
                document.getElementById('report-filter-form').submit();
            });
        });
    });
</script>
@endpush
@endsection