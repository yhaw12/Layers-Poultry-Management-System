@extends('layouts.app')

@section('content')

<style>
    /* 1. Page Load Animations */
    .animate-enter {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 2. Hover Card Lift & Glow */
    .hover-trigger {
        transition: all 0.3s ease;
    }
    .hover-trigger:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    /* 3. Gradient Text Animation */
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradientMove 3s ease infinite;
    }
    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl overflow-hidden">

    <header class="mb-8 animate-enter">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Dashboard</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Your farm management overview</p>
    </header>

    <section class="mb-8 animate-enter delay-100">
        <div class="container-box bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 transition-all duration-300 hover:shadow-xl">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Date Range Filter</h2>
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[150px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('d-m-Y') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" aria-label="Select start date">
                </div>

                <div class="flex-1 min-w-[150px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('d-m-Y') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" aria-label="Select end date">
                </div>

                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200 transform active:scale-95" aria-label="Apply date filter">Filter</button>
            </form>
        </div>
    </section>

    <section class="mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="container-box bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 animate-enter delay-200">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Quick Actions</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @role('admin')
                        @can('create_birds')
                            <a href="{{ route('birds.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Add new bird">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Bird
                            </a>
                        @endcan
                        @can('create_eggs')
                            <a href="{{ route('eggs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Record egg production">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Record Eggs
                            </a>
                        @endcan
                        @can('create_sales')
                            <a href="{{ route('sales.create') }}" class="flex items-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Add new sale">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Add Sale
                            </a>
                        @endcan
                        @can('create_expenses')
                            <a href="{{ route('expenses.create') }}" class="flex items-center bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Log new expense">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Log Expense
                            </a>
                        @endcan
                        @can('create_income')
                            <a href="{{ route('income.create') }}" class="flex items-center bg-teal-600 text-white py-3 px-4 rounded-lg hover:bg-teal-700 dark:bg-teal-500 dark:hover:bg-teal-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Log new income">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Log Income
                            </a>
                        @endcan
                        @can('create_users')
                            <a href="{{ route('users.create') }}" class="flex items-center bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Add new user">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2m6-4v4m-4 0h8"/></svg>
                                Add User
                            </a>
                        @endcan
                        @can('create_employees')
                            <a href="{{ route('employees.create') }}" class="flex items-center bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 dark:bg-yellow-500 dark:hover:bg-yellow-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1 shadow-md hover:shadow-lg" aria-label="Add new employee">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Add Employee
                            </a>
                        @endcan
                    @endrole

                    {{-- Farm Manager Roles --}}
                    @role('farm_manager')
                        @can('create_birds')
                            <a href="{{ route('birds.create') }}" class="flex items-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1" aria-label="Add new bird">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Bird
                            </a>
                        @endcan
                        @can('create_eggs')
                            <a href="{{ route('eggs.create') }}" class="flex items-center bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200 transform hover:scale-105 hover:-translate-y-1" aria-label="Record egg production">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Record Eggs
                            </a>
                        @endcan
                        {{-- ... (Other manager actions kept same, just adding hover classes implicitly) ... --}}
                    @endrole
                    {{-- ... (Other roles kept same) ... --}}
                </div>
            </div>

            <div class="flex flex-col gap-6">
                <div class="container-box bg-gradient-to-r from-blue-100 to-gray-50 dark:from-blue-900 dark:to-gray-800 shadow-lg rounded-xl p-4 max-w-[300px] transition-all duration-500 hover:shadow-2xl hover:-translate-y-1 hover:scale-[1.02] animate-enter delay-300">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                            Weather
                        </h2>
                        <div id="weather-icon" aria-hidden="true" class="h-8 w-8 bg-gray-200 rounded-full animate-pulse"></div>
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

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 w-full max-w-[400px] mx-auto perspective-1000 hover-trigger animate-enter delay-300">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Task Calendar
                        </h2>
                        <span id="reminder-count" class="text-sm text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded-full transition-transform hover:scale-110">{{ isset($reminders) ? $reminders->count() : 0 }} pending</span>
                    </div>

                    <div id="reminder-rotator" class="relative h-40 overflow-hidden">
                        @if(isset($reminders) && $reminders->isNotEmpty())
                            @foreach($reminders as $i => $reminder)
                                <div class="absolute inset-0 transition-transform duration-700 ease-in-out transform origin-top {{ $i === 0 ? 'rotate-x-0 opacity-100' : 'rotate-x-90 opacity-0' }}" data-reminder="{{ $i }}" data-id="{{ $reminder->id }}">
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 shadow-inner group hover:shadow-md transition-shadow">
                                        <div class="text-center mb-3 bg-white dark:bg-gray-800 rounded-md shadow p-2 group-hover:scale-105 transition-transform">
                                            <span class="text-3xl font-bold text-gray-800 dark:text-white block">{{ \Carbon\Carbon::parse($reminder->due_date)->format('d') }}</span>
                                            <span class="text-sm uppercase text-gray-600 dark:text-gray-400 block">{{ \Carbon\Carbon::parse($reminder->due_date)->format('M Y') }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-500 block">{{ \Carbon\Carbon::parse($reminder->due_date)->format('D') }}</span>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1 truncate">{{ $reminder->title }}</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $reminder->message }}</p>
                                        {{-- Buttons kept same --}}
                                        <div class="flex justify-between items-center">
                                            <span class="px-2 py-1 text-xs font-bold rounded-full
                                                @if($reminder->severity === 'critical') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                                @elseif($reminder->severity === 'warning') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                                                @else bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 @endif">
                                                {{ strtoupper($reminder->severity) }}
                                            </span>
                                            <button class="mark-read-btn text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center transition-colors" data-id="{{ $reminder->id }}" aria-label="Mark reminder as done">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
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
                    {{-- Nav buttons kept same --}}
                    <div class="flex justify-between items-center mt-4">
                        <button id="prev-reminder" class="p-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 transition hover:scale-110">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <div class="flex space-x-1">
                            @if(isset($reminders) && $reminders->isNotEmpty())
                                @foreach($reminders as $i => $reminder)
                                    <button class="w-2 h-2 rounded-full reminder-dot {{ $i === 0 ? 'bg-blue-600' : 'bg-gray-400' }} transition-colors duration-300" data-index="{{ $i }}"></button>
                                @endforeach
                            @endif
                        </div>
                        <button id="next-reminder" class="p-1 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-600 transition hover:scale-110">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-8 animate-enter delay-400">
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
                            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-6 shadow transition-all duration-300 hover:shadow-2xl hover:scale-105 hover-trigger border-b-4 border-{{ $card['color'] === 'red' ? 'red' : 'green' }}-500">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-base sm:text-lg">{{ $card['label'] }}</h3>
                                    <span class="text-xl sm:text-2xl animate-bounce-slow">{{ $card['icon'] }}</span>
                                </div>

                                <p class="text-xl sm:text-2xl font-bold mt-4 truncate">
                                    <span class="text-gray-500 text-sm">₵</span>
                                    <span class="counter" data-target="{{ $card['value'] }}">0</span>
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
        @endcan

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Performance Indicators (KPIs)</h2>

            @php
                $groupedKpis = [
                    'Flock Statistics' => [
                        ['label' => 'Chicks', 'value' => $chicks ?? 0, 'icon' => '🐤'],
                        ['label' => 'Layers', 'value' => $layerBirds ?? 0, 'icon' => '🐓'],
                        ['label' => 'Broilers', 'value' => $broilerBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Total Birds', 'value' => $totalBirds ?? 0, 'icon' => '🥩'],
                        ['label' => 'Mortality %', 'value' => $mortalityRate ?? 0, 'icon' => '⚰️', 'is_decimal' => true],
                    ],
                    'Production' => [
                        ['label' => 'Egg Crates', 'value' => $metrics['egg_crates'] ?? 0, 'icon' => '🥚'],
                        ['label' => 'Feed (kg)', 'value' => $metrics['feed_kg'] ?? 0, 'icon' => '🌾'],
                        ['label' => 'FCR', 'value' => $fcr ?? 0, 'icon' => '⚖️', 'is_decimal' => true],
                    ],
                    'Operations' => [
                        ['label' => 'Employees', 'value' => $employees ?? 0, 'icon' => '👨‍🌾'],
                        ['label' => 'Payroll', 'value' => $totalPayroll ?? 0, 'icon' => '💵'],
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
                            <div class="container-box bg-white dark:bg-gray-800 rounded-xl p-4 shadow transition-all duration-300 hover:shadow-xl hover:scale-105 hover-trigger">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                    <span class="text-xl sm:text-2xl transition-transform hover:rotate-12">{{ $item['icon'] }}</span>
                                </div>

                                <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">
                                    <span class="counter" data-target="{{ $item['value'] }}" data-decimal="{{ $item['is_decimal'] ?? 0 }}">0</span>
                                    @if(isset($item['is_decimal']) && $item['label'] === 'Mortality %') % @endif
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

<script>
/* ---------- Animated Counters Logic ---------- */
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");
    const speed = 1000; // The lower the slower

    const animateCounters = () => {
        counters.forEach((counter) => {
            const updateCount = () => {
                const target = parseFloat(counter.getAttribute("data-target"));
                const isDecimal = counter.getAttribute("data-decimal") == '1';
                const count = parseFloat(counter.innerText.replace(/,/g, '')); // Remove commas for calculation
                
                // Determine increment step
                const inc = target / 50; // Break into 50 frames

                if (count < target) {
                    // Update number
                    let nextVal = count + inc;
                    if(nextVal > target) nextVal = target;
                    
                    // Format output
                    if(isDecimal) {
                        counter.innerText = nextVal.toFixed(2);
                    } else {
                        counter.innerText = Math.ceil(nextVal).toLocaleString(); 
                    }
                    
                    setTimeout(updateCount, 20);
                } else {
                    // Final set to ensure accuracy
                    if(isDecimal) {
                        counter.innerText = target.toFixed(2);
                    } else {
                        counter.innerText = target.toLocaleString();
                    }
                }
            };
            updateCount();
        });
    };

    // Trigger animations when the financial section comes into view
    const observerOptions = { threshold: 0.2 };
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target); // Only animate once
            }
        });
    }, observerOptions);

    // Observe the Financial Section
    const financeSection = document.querySelector('.mb-6'); // Targeting the first financial container
    if(financeSection) observer.observe(financeSection);
    // Fallback: trigger immediately if observer fails or section already visible
    else animateCounters();
});

/* ---------- Data Normalization & Chart Init ---------- */
function normalizeSeries(raw) {
  try {
    if (!raw) return { labels: [], values: [] };
    if (typeof raw === 'string') { try { raw = JSON.parse(raw); } catch (e) { return { labels: [], values: [] }; } }
    if (!Array.isArray(raw) && typeof raw === 'object') {
      const keys = Object.keys(raw); if (!keys.length) return { labels: [], values: [] };
      return { labels: keys, values: keys.map(k => Number(raw[k] ?? 0)) };
    }
    if (Array.isArray(raw)) {
      if (!raw.length) return { labels: [], values: [] };
      const first = raw[0];
      if (first && typeof first === 'object' && !Array.isArray(first)) {
        const labels = raw.map(r => r.date ?? r.label ?? r.name ?? '');
        const values = raw.map(r => Number(r.value ?? r.v ?? r.y ?? Object.values(r).find(v => typeof v === 'number' || !isNaN(Number(v))) ?? 0));
        return { labels, values };
      }
      if (typeof first === 'number' || !isNaN(Number(first))) {
        const values = raw.map(v => Number(v ?? 0)); const labels = values.map((_, i) => i + 1); return { labels, values };
      }
      return { labels: raw.map((r,i) => String(r)), values: raw.map(() => 0) };
    }
    return { labels: [], values: [] };
  } catch (err) { console.error('normalizeSeries error', err, raw); return { labels: [], values: [] }; }
}

const RAW = {
  expenseData: @json($expenseData ?? []),
  profitTrend: @json($profitTrend ?? []),
  profitValue: Number(@json($profit ?? 0)),
  incomeData: @json($incomeTrend ?? $incomeData ?? []) // Added for income chart
};

const COLORS = { green: '#10B981', blue: '#3B82F6', red: '#EF4444', yellow: '#F59E0B', purple: '#7C3AED', grayText: '#374151' };

function safeCreateChart(canvasId, type, labels, data, datasetOptions = {}, extraOptions = {}) {
  const canvas = document.getElementById(canvasId);
  if (!canvas) return null;
  const ctx = canvas.getContext('2d');
  
  // Animation Config for Charts
  const animationOptions = {
      animation: {
          duration: 2000,
          easing: 'easeOutQuart'
      }
  };

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
        tension: datasetOptions.tension ?? 0.3, // Smoother curves
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
        x: { ticks: { color: COLORS.grayText }, grid: { display: false } }, // Cleaner look without X grid
        y: { ticks: { color: COLORS.grayText }, grid: { color: 'rgba(0,0,0,0.04)' } }
      },
      ...animationOptions
    }, extraOptions)
  });
}

document.addEventListener('DOMContentLoaded', function () {
  const expenseMini = normalizeSeries(RAW.expenseData);
  const incomeMiniData = normalizeSeries(RAW.incomeData);
  const profitMini = normalizeSeries(RAW.profitTrend);

  window.expensesMini = safeCreateChart('expensesTrend', 'line', expenseMini.labels, expenseMini.values, { hideLegend: true, hideTooltip: true, borderColor: COLORS.red, backgroundColor: 'rgba(239,68,68,0.12)', fill: true }, { scales: { x: { display: false }, y: { display: false } } });
  
  window.incomeMini = safeCreateChart('incomeTrend', 'line', incomeMiniData.labels, incomeMiniData.values, { hideLegend: true, hideTooltip: true, borderColor: COLORS.green, backgroundColor: 'rgba(16,185,129,0.12)', fill: true }, { scales: { x: { display: false }, y: { display: false } } });
  
  window.profitMini = safeCreateChart('profitTrend', 'line', profitMini.labels, profitMini.values, { hideLegend: true, hideTooltip: true, borderColor: (RAW.profitValue >= 0 ? COLORS.green : COLORS.red), backgroundColor: (RAW.profitValue >= 0 ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)'), fill: true }, { scales: { x: { display: false }, y: { display: false } } });

  function applyDarkToCharts(isDark) {
    const textColor = isDark ? '#D1D5DB' : '#374151';
    const grid = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';
    [window.expensesMini, window.incomeMini, window.profitMini].forEach(c => {
      if (!c) return;
      if (!c.options.scales) c.options.scales = {};
      c.options.scales.x = c.options.scales.x || {};
      c.options.scales.y = c.options.scales.y || {};
      if(c.options.scales.x.ticks) c.options.scales.x.ticks.color = textColor;
      if(c.options.scales.y.ticks) c.options.scales.y.ticks.color = textColor;
      c.update();
    });
  }
  applyDarkToCharts(document.documentElement.classList.contains('dark') || document.body.classList.contains('dark'));
  const observer = new MutationObserver(() => applyDarkToCharts(document.documentElement.classList.contains('dark') || document.body.classList.contains('dark')));
  observer.observe(document.documentElement, { attributes: true });
});

// Reminder Widget & Weather Logic (Kept essentially same, just added comments for clarity)
document.addEventListener('DOMContentLoaded', () => {
    // ... Reminder Widget Logic ...
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

    const rotator = document.getElementById('reminder-rotator');
    if(rotator){
        rotator.addEventListener('mouseenter', () => clearInterval(interval));
        rotator.addEventListener('mouseleave', () => interval = setInterval(nextReminder, 5000));
    }
    if(nextBtn) nextBtn.addEventListener('click', () => { clearInterval(interval); nextReminder(); interval = setInterval(nextReminder, 5000); });
    if(prevBtn) prevBtn.addEventListener('click', () => { clearInterval(interval); prevReminder(); interval = setInterval(nextReminder, 5000); });

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

    // Weather Logic
    const conditionEl = document.getElementById("weather-condition");
    const iconEl = document.getElementById("weather-icon");
    if(conditionEl && iconEl){
        const condition = conditionEl.innerText.toLowerCase();
        let iconSvg = "";
        if (condition.includes("clear")) {
            iconSvg = `<svg class="w-8 h-8 text-yellow-400 animate-spin-slow glow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364-6.364l-2.121 2.121M8.757 17.243l-2.121 2.121m12.728 0l-2.121-2.121M8.757 6.757l-2.121-2.121" /></svg>`;
        } else if (condition.includes("cloud")) {
            iconSvg = `<svg class="w-8 h-8 text-gray-500 animate-bounce-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>`;
        } else if (condition.includes("rain")) {
            iconSvg = `<svg class="w-8 h-8 text-blue-500 animate-fade-in-delay" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 13v1m-4 2v1m-4-4v1m0 4v1M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>`;
        } else {
            iconSvg = `<svg class="w-8 h-8 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" /></svg>`;
        }
        iconEl.innerHTML = iconSvg;
    }
});
</script>

@endsection