@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Date Filter -->
    <form method="GET" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[150px]">
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date"
                    value="{{ $startDate ?? now()->startOfMonth()->format('Y-m-d') }}"
                    class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date"
                    value="{{ $endDate ?? now()->endOfMonth()->format('Y-m-d') }}"
                    class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200">
            </div>
            <button type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition duration-200">
                Filter
            </button>
        </div>
    </form>

    <!-- Alerts (Admin Only) -->
    @role('admin')
        <section id="alerts-section" class="mb-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">System Alerts</h3>
                    @if($alerts->isNotEmpty())
                        <form action="{{ route('alerts.dismiss-all') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                Dismiss All
                            </button>
                        </form>
                    @endif
                </div>
                @if($alerts->isNotEmpty())
                    @foreach($alerts as $alert)
                        <div class="p-4 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-2xl mb-2 flex justify-between items-center">
                            <span>{{ $alert->message }}</span>
                            <form action="{{ route('alerts.read', $alert) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Mark as Read</button>
                            </form>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-600 dark:text-gray-400 text-center py-4">No active alerts at this time.</p>
                @endif
            </div>
        </section>
    @endrole

    <!-- Summary Section (Admin or Permission-Based) -->
    @can('manage-finances')
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Financial Summary</h2>
            @if(isset($totalExpenses, $totalIncome, $profit))
                @php
                    $cards = [
                        ['label' => 'Expenses', 'value' => $totalExpenses ?? 0, 'icon' => '💸', 'color' => 'red'],
                        ['label' => 'Income', 'value' => $totalIncome ?? 0, 'icon' => '💰', 'color' => 'green'],
                        ['label' => 'Profit', 'value' => $profit ?? 0, 'icon' => '📈', 'color' => ($profit ?? 0) >= 0 ? 'green' : 'red'],
                    ];
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($cards as $card)
                        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md hover:shadow-xl transition-transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-700 dark:text-gray-200">{{ $card['label'] }}</h3>
                                <span class="text-2xl text-{{ $card['color'] }}-500">{{ $card['icon'] }}</span>
                            </div>
                            <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-4">
                                ${{ number_format($card['value'], 2) }}
                            </p>
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
        <section>
            <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 p-4 rounded-2xl" role="alert">
                You do not have permission to view the financial summary.
            </div>
        </section>
    @endcan

    <!-- KPIs Section -->
    <section>
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
        @foreach($groupedKpis as $group => $kpis)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $group }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($kpis as $item)
                        @if($group === 'Operations' && in_array($item['label'], ['Employees', 'Payroll', 'Sales', 'Customers']))
                            @role('admin')
                                <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md hover:shadow-xl transition-transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-gray-700 dark:text-gray-300 font-medium">{{ $item['label'] }}</h4>
                                        <span class="text-2xl">{{ $item['icon'] }}</span>
                                    </div>
                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $item['value'] }}</p>
                                </div>
                            @endrole
                        @else
                            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md hover:shadow-xl transition-transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-gray-700 dark:text-gray-300 font-medium">{{ $item['label'] }}</h4>
                                    <span class="text-2xl">{{ $item['icon'] }}</span>
                                </div>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $item['value'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <!-- Trend Charts -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Production Trends</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Egg Trend -->
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium dark:text-gray-200">Egg Trend</h4>
                    <select id="eggChartType" class="dark:bg-gray-800 dark:text-white p-2 rounded border dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <div class="relative">
                    <div id="eggTrendLoading" class="hidden absolute inset-0 bg-gray-200 dark:bg-gray-700 animate-pulse rounded-lg"></div>
                    <canvas id="eggTrend" class="w-full"></canvas>
                </div>
            </div>

            <!-- Feed Trend -->
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium dark:text-gray-200">Feed Trend</h4>
                    <select id="feedChartType" class="dark:bg-gray-800 dark:text-white p-2 rounded border dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <div class="relative">
                    <div id="feedTrendLoading" class="hidden absolute inset-0 bg-gray-200 dark:bg-gray-700 animate-pulse rounded-lg"></div>
                    <canvas id="feedTrend" class="w-full"></canvas>
                </div>
            </div>

            <!-- Sales Trend (Admin Only) -->
            @role('admin')
                <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium dark:text-gray-200">Sales Trend</h4>
                        <select id="salesChartType" class="dark:bg-gray-800 dark:text-white p-2 rounded border dark:border-gray-600 focus:ring-2 focus:ring-blue-500">
                            <option value="line">Line</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>
                    <div class="relative">
                        <div id="salesTrendLoading" class="hidden absolute inset-0 bg-gray-200 dark:bg-gray-700 animate-pulse rounded-lg"></div>
                        <canvas id="salesTrend" class="w-full"></canvas>
                    </div>
                </div>
            @endrole
        </div>
    </section>
</div>

@section('scripts')


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let eggChart, feedChart, salesChart;

    function showLoading(chartId) {
        document.getElementById(`${chartId}Loading`).classList.remove('hidden');
    }

    function hideLoading(chartId) {
        document.getElementById(`${chartId}Loading`).classList.add('hidden');
    }

    function updateEggChart() {
        showLoading('eggTrend');
        const ctx = document.getElementById('eggTrend').getContext('2d');
        if (eggChart) eggChart.destroy();
        eggChart = new Chart(ctx, {
            type: document.getElementById('eggChartType').value,
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
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    title: { display: true, text: 'Egg Production Trend', font: { size: 16 } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Crates' } },
                    x: { title: { display: true, text: 'Date' } }
                },
                animation: {
                    onComplete: () => hideLoading('eggTrend')
                }
            }
        });
    }

    function updateFeedChart() {
        showLoading('feedTrend');
        const ctx = document.getElementById('feedTrend').getContext('2d');
        if (feedChart) feedChart.destroy();
        feedChart = new Chart(ctx, {
            type: document.getElementById('feedChartType').value,
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
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    title: { display: true, text: 'Feed Consumption Trend', font: { size: 16 } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Kilograms' } },
                    x: { title: { display: true, text: 'Date' } }
                },
                animation: {
                    onComplete: () => hideLoading('feedTrend')
                }
            }
        });
    }

    function updateSalesChart() {
        showLoading('salesTrend');
        const ctx = document.getElementById('salesTrend').getContext('2d');
        if (salesChart) salesChart.destroy();
        salesChart = new Chart(ctx, {
            type: document.getElementById('salesChartType').value,
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
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    title: { display: true, text: 'Sales Trend', font: { size: 16 } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                    x: { title: { display: true, text: 'Date' } }
                },
                animation: {
                    onComplete: () => hideLoading('salesTrend')
                }
            }
        });
    }

    @if (isset($eggTrend) && $eggTrend->isNotEmpty())
        updateEggChart();
    @else
        document.getElementById('eggTrend').parentElement.insertAdjacentHTML('beforeend', '<p class="text-gray-600 dark:text-gray-400 text-center py-4">No egg data available.</p>');
        hideLoading('eggTrend');
    @endif

    @if (isset($feedTrend) && $feedTrend->isNotEmpty())
        updateFeedChart();
    @else
        document.getElementById('feedTrend').parentElement.insertAdjacentHTML('beforeend', '<p class="text-gray-600 dark:text-gray-400 text-center py-4">No feed data available.</p>');
        hideLoading('feedTrend');
    @endif

    @if (isset($salesTrend) && $salesTrend->isNotEmpty())
        updateSalesChart();
    @else
        @role('admin')
            document.getElementById('salesTrend').parentElement.insertAdjacentHTML('beforeend', '<p class="text-gray-600 dark:text-gray-400 text-center py-4">No sales data available.</p>');
            hideLoading('salesTrend');
        @endrole
    @endif

    // Add change event listeners for chart type selectors
    document.getElementById('eggChartType')?.addEventListener('change', updateEggChart);
    document.getElementById('feedChartType')?.addEventListener('change', updateFeedChart);
    document.getElementById('salesChartType')?.addEventListener('change', updateSalesChart);
</script>
@endsection