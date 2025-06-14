@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Date Filter -->
    <form method="GET" class="bg-white p-6 rounded shadow dark:bg-[#1a1a3a] dark:text-white">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">Start Date</label>
                <input type="date" name="start_date"
                    value="{{ $start ?? now()->startOfMonth()->format('Y-m-d') }}"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">End Date</label>
                <input type="date" name="end_date"
                    value="{{ $end ?? now()->endOfMonth()->format('Y-m-d') }}"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <button type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Filter
            </button>
        </div>
    </form>

    <!-- Alerts -->
    @if($alerts->isNotEmpty())
        <section class="mb-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">System Alerts</h3>
                @foreach($alerts as $alert)
                    <div
                      class="p-4 {{ $alert->type === 'backup_failed' ? 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' : 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' }} rounded-2xl mb-2">
                        {{ $alert->message }}
                        <form action="{{ route('alerts.read', $alert) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline">Mark as Read</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Summary Section -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Summary</h2>
        @php
            $cards = [
                ['label'=>'Expenses','value'=>$totalExpenses??0,'icon'=>'💸','color'=>'red'],
                ['label'=>'Income','value'=>$totalIncome??0,'icon'=>'💰','color'=>'green'],
                ['label'=>'Profit','value'=>$profit??0,'icon'=>'📈','color'=>($profit??0)>=0?'green':'red'],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($cards as $card)
                <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md hover:shadow-xl transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">{{ $card['label'] }}</h3>
                        <span class="text-2xl text-{{ $card['color'] }}-500">{{ $card['icon'] }}</span>
                    </div>
                    <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400 mt-4">
                        {{ number_format($card['value'], 2) }}
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- KPIs Section -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Key Performance Indicators (KPIs)</h2>
        @php
            $groupedKpis = [
                'Flock Statistics'=>[
                    ['label'=>'Chicks','value'=>$chicks??0,'icon'=>'🐤'],
                    ['label'=>'Layers','value'=>$layers??0,'icon'=>'🐓'],
                    ['label'=>'Broilers','value'=>$broilers??0,'icon'=>'🥩'],
                    ['label'=>'Mortality %','value'=>number_format($mortalityRate??0,2),'icon'=>'⚰️'],
                ],
                'Production'=>[
                    ['label'=>'Egg Crate','value'=>$metrics['egg_crates']??0,'icon'=>'🥚'],
                    ['label'=>'Feed (kg)','value'=>$metrics['feed_kg']??0,'icon'=>'🌾'],
                    ['label'=>'FCR','value'=>$fcr??0,'icon'=>'⚖️'],
                ],
                'Operations'=>[
                    ['label'=>'Employees','value'=>$employees??0,'icon'=>'👨‍🌾'],
                    ['label'=>'Payroll','value'=>number_format($payroll??0,2),'icon'=>'💵'],
                    ['label'=>'Sales','value'=>$metrics['sales']??0,'icon'=>'🛒'],
                    ['label'=>'Customers','value'=>$metrics['customers']??0,'icon'=>'👥'],
                    ['label'=>'Med Bought','value'=>$metrics['medicine_buy']??0,'icon'=>'💊'],
                    ['label'=>'Med Used','value'=>$metrics['medicine_use']??0,'icon'=>'🩺'],
                ],
            ];
        @endphp
        @foreach($groupedKpis as $group=>$kpis)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $group }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($kpis as $item)
                        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow hover:shadow-lg transition-transform">
                            <div class="flex items-center justify-between">
                                <h4 class="text-gray-700 dark:text-gray-300 font-medium">{{ $item['label'] }}</h4>
                                <span class="text-2xl">{{ $item['icon'] }}</span>
                            </div>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $item['value'] }}</p>
                        </div>
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
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium dark:text-gray-200">Egg Trend</h4>
                    <select id="eggChartType" onchange="updateEggChart()" class="dark:bg-gray-800 dark:text-white p-2 rounded">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <canvas id="eggTrend" class="w-full"></canvas>
            </div>

            <!-- Feed Trend -->
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium dark:text-gray-200">Feed Trend</h4>
                    <select id="feedChartType" onchange="updateFeedChart()" class="dark:bg-gray-800 dark:text-white p-2 rounded">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <canvas id="feedTrend" class="w-full"></canvas>
            </div>

            <!-- Payroll Trend -->
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium dark:text-gray-200">Payroll Trend</h4>
                    <select id="payrollChartType" onchange="updatePayrollChart()" class="dark:bg-gray-800 dark:text-white p-2 rounded">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <canvas id="payrollTrend" class="w-full"></canvas>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let eggChart, feedChart, payrollChart;

    function updateEggChart() {
        const ctx = document.getElementById('eggTrend').getContext('2d');
        if (eggChart) eggChart.destroy();
        eggChart = new Chart(ctx, {
            type: document.getElementById('eggChartType').value,
            data: {
                labels: @json($eggTrend->pluck('date')),
                datasets: [{ label: 'Egg Crates', data: @json($eggTrend->pluck('value')), fill: false }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: { title: { display: true, text: 'Egg Production Trend' } }
            }
        });
    }

    function updateFeedChart() {
        const ctx = document.getElementById('feedTrend').getContext('2d');
        if (feedChart) feedChart.destroy();
        feedChart = new Chart(ctx, {
            type: document.getElementById('feedChartType').value,
            data: {
                labels: @json($feedTrend->pluck('date')),
                datasets: [{ label: 'Feed (kg)', data: @json($feedTrend->pluck('value')), fill: false }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: { title: { display: true, text: 'Feed Consumption Trend' } }
            }
        });
    }

    function updatePayrollChart() {
        const ctx = document.getElementById('payrollTrend').getContext('2d');
        if (payrollChart) payrollChart.destroy();
        payrollChart = new Chart(ctx, {
            type: document.getElementById('payrollChartType').value,
            data: {
                labels: @json($payrollTrend->pluck('date')),
                datasets: [{ label: 'Payroll (Net Pay)', data: @json($payrollTrend->pluck('value')), fill: false }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: { title: { display: true, text: 'Payroll Trend' } }
            }
        });
    }
     // Sales Trend Chart
        @if (isset($salesTrend) && $salesTrend->isNotEmpty())
            const salesTrend = new Chart(document.getElementById('salesTrend'), {
                type: 'line',
                data: {
                    labels: @json($salesTrend->pluck('date')),
                    datasets: [{ label: 'Sales ($)', data: @json($salesTrend->pluck('value')), borderColor: '#3b82f6', fill: true }]
                },
                options: { responsive: true, maintainAspectRatio: true, aspectRatio: 2 }
            });
        @else
            document.getElementById('salesTrend').parentElement.insertAdjacentHTML('beforeend', '<p class="text-gray-600 dark:text-gray-400 text-center py-4">No sales data available.</p>');
        @endif

    // initial draws
    updateEggChart();
    updateFeedChart();
    updatePayrollChart();
</script>
@endsection
