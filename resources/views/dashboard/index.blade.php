{{-- dashboard --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-7xl overflow-hidden">
        <!-- Date Filter -->
        <section class="mb-8">
            <form method="GET" class="container-box">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[150px]">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200">
                        Filter
                    </button>
                </div>
            </form>
        </section>

        <!-- Daily Instructions (Labourer) -->
        @role('labourer')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daily Instructions</h2>
                <div class="container-box">
                    @if ($dailyInstructions->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($dailyInstructions as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->content }}</span> (Posted: {{ $item->created_at->format('Y-m-d H:i') }})
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No instructions for today.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Quick Actions -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @can('create_sales')
                    <a href="{{ route('sales.create') }}" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-center transition duration-200">Add Sale</a>
                @endcan
                @can('create_expenses')
                    <a href="{{ route('expenses.create') }}" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-center transition duration-200">Log Expense</a>
                @endcan
                @can('create_eggs')
                    <a href="{{ route('eggs.create') }}" class="bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-center transition duration-200">Record Egg Production</a>
                @endcan
            </div>
        </section>

        <!-- Production Input Shortcut (Non-Admins) -->
        @unlessrole('admin')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Quick Production Log</h2>
                <div class="container-box">
                    <form action="{{ route('eggs.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="production-form">
                        @csrf
                        <div>
                            <label for="crates" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Egg Crates</label>
                            <input type="number" id="crates" name="crates" step="0.01" min="0" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Enter crates">
                            @error('crates') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="date_laid" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Laid</label>
                            <input type="date" id="date_laid" name="date_laid" value="{{ now()->toDateString() }}" class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
                            @error('date_laid') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200 w-full sm:w-auto">Log Production</button>
                        </div>
                    </form>
                </div>
            </section>
        @endunlessrole

        <!-- Flock Health Summary (Farm Manager) -->
        @role('farm_manager')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flock Health Summary</h2>
                <div class="container-box">
                    @if ($healthSummary->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($healthSummary as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->date->format('Y-m-d') }}</span>: {{ $item->checks }} checks, {{ $item->unhealthy }} unhealthy
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No recent health checks.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Vaccination Schedule (Veterinarian) -->
        @role('veterinarian')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Vaccination Schedule</h2>
                <div class="container-box">
                    @if ($vaccinationSchedule->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($vaccinationSchedule as $item)
                                <li class="list-item">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            <span class="highlight">{{ $item->vaccine_name }}</span> (Due: {{ $item->due_date->format('Y-m-d') }})
                                        </span>
                                        <form action="{{ route('vaccinations.complete', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Mark as Complete</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No upcoming vaccinations.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Supplier Quick Links (Inventory Manager) -->
        @role('inventory_manager')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Suppliers</h2>
                <div class="container-box">
                    @if ($suppliers->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($suppliers as $item)
                                <li class="list-item">
                                    <div class="flex justify-between items-center">
                                        <span>
                                            <span class="highlight">{{ $item->name }}</span> ({{ $item->contact_info }})
                                        </span>
                                        <div>
                                            <a href="{{ route('inventory.create', ['supplier_id' => $item->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mr-4">Add Inventory</a>
                                            <a href="{{ route('suppliers.show', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View Details</a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No suppliers found.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Alerts (Admin Only) -->
        @role('admin')
            <section id="alerts-section" class="mb-8 relative">
                <div class="container-box">
                    <button id="close-alerts" class="absolute top-4 right-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none" title="Close Alerts">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    @if (session('error'))
                        <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 p-4 rounded-2xl mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-2xl mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Alerts</h3>
                        @if ($alerts->isNotEmpty())
                            <form action="{{ route('alerts.dismiss-all') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                    Dismiss All
                                </button>
                            </form>
                        @endif
                    </div>
                    <div id="alerts-content">
                        @if ($alerts->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach ($alerts as $item)
                                    <li class="list-item">
                                        <div class="flex justify-between items-center">
                                            <span class="{{ $item->type === 'warning' ? 'text-red-600' : '' }}">{{ $item->message }}</span>
                                            @if ($item->user_id)
                                                <form action="{{ route('alerts.read', $item) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Mark as Read</button>
                                                </form>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="no-data">No active alerts at this time.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endrole

        <!-- Financial Summary (Admins with Permission) -->
        @can('manage_finances')
            <section class="mb-8">
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
                            <div class="container-box">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-700 dark:text-gray-200 text-base sm:text-lg">{{ $card['label'] }}</h3>
                                    <span class="text-xl sm:text-2xl text-{{ $card['color'] }}-500">{{ $card['icon'] }}</span>
                                </div>
                                <p class="text-xl sm:text-2xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-4 truncate">
                                    ${{ number_format($card['value'], 2) }}
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
            </section>
        @else
            <section class="mb-8">
                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded-2xl" role="alert">
                    You do not have permission to view the financial summary.
                </div>
            </section>
        @endcan

        <!-- Payroll Status (Accountant) -->
        @role('accountant')
            <section class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Payroll Status</h2>
                <div class="container-box">
                    @if ($payrollStatus->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($payrollStatus as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->date->format('Y-m-d') }}</span>: {{ $item->employees }} employees, ${{ number_format($item->total, 2) }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No recent payroll activity.</p>
                    @endif
                </div>
            </section>
        @endrole

        <!-- Key Performance Indicators (KPIs) -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Performance Indicators (KPIs)</h2>
            @php
                $groupedKpis = [
                    'Flock Statistics' => [
                        ['label' => 'Chicks', 'value' => $chicks ?? 0, 'icon' => '🐤'],
                        ['label' => 'Layers', 'value' => $layers ?? 0, 'icon' => '🐓'],
                        ['label' => 'Broilers', 'value' => $broilers ?? 0, 'icon' => '🥩'],
                        ['label' => 'Mortality %', 'value' => number_format($mortalityRate ?? 0, 2), 'icon' => '⚰️'],
                    ],
                    'Production' => [
                        ['label' => 'Egg Crates', 'value' => $metrics['egg_crates'] ?? 0, 'icon' => '🥚'],
                        ['label' => 'Feed (kg)', 'value' => $metrics['feed_kg'] ?? 0, 'icon' => '🌾'],
                        ['label' => 'FCR', 'value' => number_format($fcr ?? 0, 2), 'icon' => '⚖️'],
                    ],
                    'Operations' => [
                        ['label' => 'Employees', 'value' => $employees ?? 0, 'icon' => '👨‍🌾'],
                        ['label' => 'Payroll', 'value' => number_format($payroll ?? 0, 2), 'icon' => '💵'],
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
                            @if ($group === 'Operations' && in_array($item['label'], ['Employees', 'Payroll', 'Sales', 'Customers']))
                                @role('admin')
                                    <div class="container-box">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                            <span class="text-xl sm:text-2xl">{{ $item['icon'] }}</span>
                                        </div>
                                        <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $item['value'] }}</p>
                                    </div>
                                @endrole
                            @else
                                <div class="container-box">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-gray-700 dark:text-gray-300 font-medium text-base sm:text-lg">{{ $item['label'] }}</h4>
                                        <span class="text-xl sm:text-2xl">{{ $item['icon'] }}</span>
                                    </div>
                                    <p class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2 truncate">{{ $item['value'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Recent Activity -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Recent Activity</h2>
            <div class="container-box">
                <div id="recent-activity-content">
                    @if ($recentActivities->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach ($recentActivities as $item)
                                <li class="list-item">
                                    <span class="highlight">{{ $item->action }}</span> by {{ $item->user_name }} on {{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="no-data">No recent activity.</p>
                    @endif
                </div>
            </div>
        </section>

        <!-- Production Trends -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Production Trends</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="chart-container">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title">Egg Trend</h4>
                        <select id="eggChartType" class="chart-select">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>
                    <div class="relative h-64">
                        <canvas id="eggTrend" class="w-full h-full"></canvas>
                    </div>
                    @if (!isset($eggTrend) || $eggTrend->isEmpty())
                        <p class="no-data">No egg data available.</p>
                    @endif
                </div>
                <div class="chart-container">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="chart-title">Feed Trend</h4>
                        <select id="feedChartType" class="chart-select">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>
                    <div class="relative h-64">
                        <canvas id="feedTrend" class="w-full h-full"></canvas>
                    </div>
                    @if (!isset($feedTrend) || $feedTrend->isEmpty())
                        <p class="no-data">No feed data available.</p>
                    @endif
                </div>
                @role('admin')
                    <div class="chart-container">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="chart-title">Sales Trend</h4>
                            <select id="salesChartType" class="chart-select">
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                        <div class="relative h-64">
                            <canvas id="salesTrend" class="w-full h-full"></canvas>
                        </div>
                        @if (!isset($salesTrend) || $salesTrend->isEmpty())
                            <p class="no-data">No sales data available.</p>
                        @endif
                    </div>
                @endrole
            </div>
        </section>

        <!-- Income Trend -->
        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Income Trend</h2>
            <div class="chart-container">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="chart-title">Income Trend</h4>
                    <select id="incomeChartType" class="chart-select">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <div class="relative h-64">
                    <canvas id="incomeChart" class="w-full h-full"></canvas>
                </div>
                @if (!isset($incomeLabels) || empty($incomeLabels))
                    <p class="no-data">No income data available.</p>
                @endif
            </div>
        </section>

        <!-- Invoice Status (Admin Only) -->
        @role('admin')
            <section class="mb-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Invoice Status</h2>
                    <form action="{{ route('dashboard.export') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200">
                            Export to CSV
                        </button>
                    </form>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="chart-container">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="chart-title">Invoice Status Distribution</h4>
                            <select id="invoiceChartType" class="chart-select">
                                <option value="line">Line</option>
                                <option value="bar">Bar</option>
                            </select>
                        </div>
                        <div class="relative h-64">
                            <canvas id="invoiceStatus" class="w-full h-full"></canvas>
                        </div>
                        @if (!isset($invoiceStatuses) || array_sum($invoiceStatuses) == 0)
                            <p class="no-data">No invoice status data available.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endrole
    </div>
@endsection

@push('scripts')
    <script>
        let eggChart, feedChart, salesChart, invoiceStatusChart, expensesTrendChart, incomeTrendChart, profitTrendChart;

        function createChart(canvasId, typeSelectorId, data, config) {
            try {
                globalLoader.show(`Loading ${config.title}...`);
                const ctx = document.getElementById(canvasId)?.getContext('2d');
                if (!ctx) throw new Error(`Canvas ${canvasId} not found`);

                // Destroy existing chart instance if it exists
                if (window[canvasId + 'Chart']) {
                    window[canvasId + 'Chart'].destroy();
                }

                // Validate data
                if (!config.data.labels || !config.data.datasets || !config.data.datasets[0].data) {
                    throw new Error(`Invalid data for chart ${canvasId}`);
                }

                window[canvasId + 'Chart'] = new Chart(ctx, {
                    type: document.getElementById(typeSelectorId)?.value || config.type,
                    data: config.data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: { display: true, text: config.title, font: { size: 16 } },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: config.yAxis } },
                            x: { title: { display: true, text: config.xAxis } }
                        },
                        animation: {
                            onComplete: () => {
                                console.log(`Chart ${canvasId} rendered successfully`);
                                globalLoader.hide();
                            }
                        }
                    }
                });

                const typeSelector = document.getElementById(typeSelectorId);
                if (typeSelector) {
                    typeSelector.addEventListener('change', () => createChart(canvasId, typeSelectorId, data, config));
                }
            } catch (error) {
                console.error(`Failed to create chart ${canvasId}:`, error);
                notificationManager.show(Date.now(), `Failed to load ${config.title}.`, 'critical', 5000);
                globalLoader.hide();
            }
        }

        function createSparklineChart(canvasId, data, color) {
            try {
                globalLoader.show('Loading trend data...');
                const ctx = document.getElementById(canvasId)?.getContext('2d');
                if (!ctx) throw new Error(`Canvas ${canvasId} not found`);

                // Destroy existing chart instance if it exists
                if (window[canvasId + 'Chart']) {
                    window[canvasId + 'Chart'].destroy();
                }

                // Validate data
                if (!data || !Array.isArray(data) || data.length === 0) {
                    throw new Error(`Invalid data for sparkline chart ${canvasId}`);
                }

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(d => d.date),
                        datasets: [{
                            data: data.map(d => d.value),
                            borderColor: color,
                            backgroundColor: color,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        },
                        scales: {
                            x: { display: false },
                            y: { display: false }
                        },
                        animation: {
                            onComplete: () => {
                                console.log(`Sparkline ${canvasId} rendered successfully`);
                                globalLoader.hide();
                            }
                        }
                    }
                });
            } catch (error) {
                console.error(`Failed to create sparkline chart ${canvasId}:`, error);
                notificationManager.show(Date.now(), 'Failed to load trend data.', 'critical', 5000);
                globalLoader.hide();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Fallback to hide loader after 15 seconds
            setTimeout(() => {
                if (globalLoader.isShowing) {
                    console.warn('Fallback: Hiding loader after 15 seconds');
                    globalLoader.hide();
                }
            }, 15000);

            // Load Alerts (Admin Only)
            @role('admin')
                try {
                    const alertsContent = document.getElementById('alerts-content');
                    if (alertsContent) {
                        globalLoader.show('Loading alerts...');
                        console.log('Loading alerts section');
                        const alertsExist = @json($alerts->isNotEmpty());
                        const sessionMessages = @json(session('error') || session('success'));
                        if (!alertsExist && !sessionMessages) {
                            document.getElementById('alerts-section').style.display = 'none';
                        }
                    }
                } catch (error) {
                    console.error('Failed to load alerts:', error);
                    notificationManager.show(Date.now(), 'Failed to load alerts.', 'critical', 5000);
                } finally {
                    globalLoader.hide();
                }
            @endrole

            // Load Recent Activity
            try {
                const recentActivityContent = document.getElementById('recent-activity-content');
                if (recentActivityContent) {
                    globalLoader.show('Loading recent activity...');
                    console.log('Loading recent activity section');
                    if (!@json($recentActivities->isNotEmpty())) {
                        recentActivityContent.innerHTML = '<p class="no-data">No recent activity.</p>';
                    }
                }
            } catch (error) {
                console.error('Failed to load recent activity:', error);
                notificationManager.show(Date.now(), 'Failed to load recent activity.', 'critical', 5000);
            } finally {
                globalLoader.hide();
            }

            // Load Charts
            @if (isset($eggTrend) && $eggTrend->isNotEmpty())
                createChart('eggTrend', 'eggChartType', @json($eggTrend), {
                    type: 'line',
                    title: 'Egg Production Trend',
                    yAxis: 'Crates',
                    xAxis: 'Date',
                    data: {
                        labels: @json($eggTrend->pluck('date')),
                        datasets: [{
                            label: 'Egg Crates',
                            data: @json($eggTrend->pluck('value')),
                            fill: false,
                            borderColor: '#10b981',
                            backgroundColor: '#10b981',
                            tension: 0.1
                        }]
                    }
                });
            @else
                try {
                    const eggTrendContainer = document.getElementById('eggTrend')?.parentElement;
                    if (eggTrendContainer && !eggTrendContainer.querySelector('.no-data')) {
                        eggTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No egg data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($feedTrend) && $feedTrend->isNotEmpty())
                createChart('feedTrend', 'feedChartType', @json($feedTrend), {
                    type: 'line',
                    title: 'Feed Consumption Trend',
                    yAxis: 'Kilograms',
                    xAxis: 'Date',
                    data: {
                        labels: @json($feedTrend->pluck('date')),
                        datasets: [{
                            label: 'Feed (kg)',
                            data: @json($feedTrend->pluck('value')),
                            fill: false,
                            borderColor: '#f97316',
                            backgroundColor: '#f97316',
                            tension: 0.1
                        }]
                    }
                });
            @else
                try {
                    const feedTrendContainer = document.getElementById('feedTrend')?.parentElement;
                    if (feedTrendContainer && !feedTrendContainer.querySelector('.no-data')) {
                        feedTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No feed data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($salesTrend) && $salesTrend->isNotEmpty())
                @can('view-sales')
                    createChart('salesTrend', 'salesChartType', @json($salesTrend), {
                        type: 'line',
                        title: 'Sales Trend',
                        yAxis: 'Amount ($)',
                        xAxis: 'Date',
                        data: {
                            labels: @json($salesTrend->pluck('date')),
                            datasets: [{
                                label: 'Sales ($)',
                                data: @json($salesTrend->pluck('value')),
                                fill: false,
                                borderColor: '#3b82f6',
                                backgroundColor: '#3b82f6',
                                tension: 0.1
                            }]
                        }
                    });
                @else
                    try {
                        const salesTrendContainer = document.getElementById('salesTrend')?.parentElement;
                        if (salesTrendContainer && !salesTrendContainer.querySelector('.no-data')) {
                            salesTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No sales data available.</p>');
                        }
                    } finally {
                        globalLoader.hide();
                    }
                @endcan
            @endif

            @if (isset($incomeLabels) && !empty($incomeLabels))
                createChart('incomeChart', 'incomeChartType', @json($incomeData), {
                    type: 'bar',
                    title: 'Income Trend',
                    yAxis: 'Amount ($)',
                    xAxis: 'Month',
                    data: {
                        labels: @json($incomeLabels),
                        datasets: [{
                            label: 'Monthly Income',
                            data: @json($incomeData),
                            borderColor: '#4A90E2',
                            backgroundColor: '#4A90E2',
                            fill: false,
                            tension: 0.1
                        }]
                    }
                });
            @else
                try {
                    const incomeChartContainer = document.getElementById('incomeChart')?.parentElement;
                    if (incomeChartContainer && !incomeChartContainer.querySelector('.no-data')) {
                        incomeChartContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No income data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($invoiceStatuses) && array_sum($invoiceStatuses) > 0)
                createChart('invoiceStatus', 'invoiceChartType', @json($invoiceStatuses), {
                    type: 'bar',
                    title: 'Invoice Status Distribution',
                    yAxis: 'Count',
                    xAxis: 'Status',
                    data: {
                        labels: ['Pending', 'Paid', 'Partially Paid', 'Overdue'],
                        datasets: [{
                            label: 'Invoice Count',
                            data: [
                                @json($invoiceStatuses['pending'] ?? 0),
                                @json($invoiceStatuses['paid'] ?? 0),
                                @json($invoiceStatuses['partially_paid'] ?? 0),
                                @json($invoiceStatuses['overdue'] ?? 0)
                            ],
                            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
                            borderColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
                            borderWidth: 1
                        }]
                    }
                });
            @else
                try {
                    const invoiceContainer = document.getElementById('invoiceStatus')?.parentElement;
                    if (invoiceContainer && !invoiceContainer.querySelector('.no-data')) {
                        invoiceContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No invoice status data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($expenseTrend) && !empty($expenseTrend))
                createSparklineChart('expensesTrend', @json($expenseTrend), '#ef4444');
            @else
                try {
                    const expensesTrendContainer = document.getElementById('expensesTrend')?.parentElement;
                    if (expensesTrendContainer && !expensesTrendContainer.querySelector('.no-data')) {
                        expensesTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No expense trend data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($incomeTrend) && !empty($incomeTrend))
                createSparklineChart('incomeTrend', @json($incomeTrend), '#10b981');
            @else
                try {
                    const incomeTrendContainer = document.getElementById('incomeTrend')?.parentElement;
                    if (incomeTrendContainer && !incomeTrendContainer.querySelector('.no-data')) {
                        incomeTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No income trend data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            @if (isset($profitTrend) && !empty($profitTrend))
                createSparklineChart('profitTrend', @json($profitTrend), @json(($profit ?? 0) >= 0 ? '#10b981' : '#ef4444'));
            @else
                try {
                    const profitTrendContainer = document.getElementById('profitTrend')?.parentElement;
                    if (profitTrendContainer && !profitTrendContainer.querySelector('.no-data')) {
                        profitTrendContainer.insertAdjacentHTML('beforeend', '<p class="no-data">No profit trend data available.</p>');
                    }
                } finally {
                    globalLoader.hide();
                }
            @endif

            // Close Alerts Section if Empty
            const alertsSection = document.getElementById('alerts-section');
            const closeAlertsBtn = document.getElementById('close-alerts');
            if (alertsSection && closeAlertsBtn) {
                closeAlertsBtn.addEventListener('click', () => {
                    alertsSection.style.display = 'none';
                });
            }
        });
    </script>
@endpush
