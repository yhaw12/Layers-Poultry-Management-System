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

    <!-- Flock Statistics -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Flock Statistics</h2>
        @php
            $flockKpis = [
                ['label'=>'Chicks','value'=>$chicks??0,'icon'=>'🐤'],
                ['label'=>'Layers','value'=>$layers??0,'icon'=>'🐓'],
                ['label'=>'Broilers','value'=>$broilers??0,'icon'=>'🥩'],
                ['label'=>'Mortality %','value'=>number_format($mortalityRate??0,2),'icon'=>'⚰️'],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($flockKpis as $item)
                <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow hover:shadow-lg transition-transform">
                    <div class="flex items-center justify-between">
                        <h4 class="text-gray-700 dark:text-gray-300 font-medium">{{ $item['label'] }}</h4>
                        <span class="text-2xl">{{ $item['icon'] }}</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $item['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Production Metrics -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Production Metrics</h2>
        @php
            $productionKpis = [
                ['label'=>'Egg Crate','value'=>$metrics['egg_crates']??0,'icon'=>'🥚'],
                ['label'=>'Feed (kg)','value'=>$metrics['feed_kg']??0,'icon'=>'🌾'],
                ['label'=>'FCR','value'=>$fcr??0,'icon'=>'⚖️'],
                ['label'=>'Med Bought','value'=>$metrics['medicine_buy']??0,'icon'=>'💊'],
                ['label'=>'Med Used','value'=>$metrics['medicine_use']??0,'icon'=>'🩺'],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($productionKpis as $item)
                <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow hover:shadow-lg transition-transform">
                    <div class="flex items-center justify-between">
                        <h4 class="text-gray-700 dark:text-gray-300 font-medium">{{ $item['label'] }}</h4>
                        <span class="text-2xl">{{ $item['icon'] }}</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $item['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Trend Charts -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Production Trends</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Egg Trend -->
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded-2xl shadow">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium dark:text-gray-200">Egg Trend</h4>
                    <select id="eggChartType" onchange="updateEggChart()" class="dark:bg-gray-800 dark:text-white p-2 rounded">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                    </select>
                </div>
                <div class="h-[80px] max-h-[80px] max-w-full overflow-hidden">
                    <canvas id="eggTrend"></canvas>
                </div>
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
                <div class="h-[80px] max-h-[80px] max-w-full overflow-hidden">
                    <canvas id="feedTrend"></canvas>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    let eggChart, feedChart;

    function updateEggChart() {
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
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                scales: {
                    y: { beginAtZero: true, max: Math.max(...@json($eggTrend->pluck('value')->toArray())) * 1.1 || 10 }
                },
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
                datasets: [{
                    label: 'Feed (kg)',
                    data: @json($feedTrend->pluck('value')),
                    fill: false,
                    borderColor: '#f97316',
                    backgroundColor: '#f97316'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                scales: {
                    y: { beginAtZero: true, max: Math.max(...@json($feedTrend->pluck('value')->toArray())) * 1.1 || 10 }
                },
                plugins: { title: { display: true, text: 'Feed Consumption Trend' } }
            }
        });
    }

    // Initial draws
    @if (isset($eggTrend) && $eggTrend->isNotEmpty())
        updateEggChart();
    @else
        document.getElementById('eggTrend').parentElement.insertAdjacentHTML('beforeend', '<p class="text-gray-600 dark:text-gray-400 text-center py-4">No egg data available.</p>');
    @endif

    @if (isset($feedTrend) && $feedTrend->isNotEmpty())
        updateFeedChart();
    @else
        document.getElementById('feedTrend').parentElement.insertAdjacentHTML('beforeend', '<p class="text-gray-600 dark:text-gray-400 text-center py-4">No feed data available.</p>');
    @endif
</script>
@endsection