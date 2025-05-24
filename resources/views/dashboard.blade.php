@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">

    <!-- Date Filter -->
    <form method="GET" class="bg-white p-6 rounded shadow dark:bg-[#1a1a3a] dark:text-white">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">Start Date</label>
                <input type="date" name="start_date" value="{{ $start }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700 dark:text-gray-300">End Date</label>
                <input type="date" name="end_date" value="{{ $end }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Filter
            </button>
        </div>
    </form>

    <!-- Summary Section -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Summary</h2>
        @php
            $cards = [
                ['label'=>'Expenses', 'value'=>$totalExpenses, 'icon'=>'💸', 'color'=>'red'],
                ['label'=>'Income', 'value'=>$totalIncome,  'icon'=>'💰', 'color'=>'green'],
                ['label'=>'Profit',  'value'=>$profit,       'icon'=>'📈', 'color'=>$profit>=0?'green':'red'],
            ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($cards as $card)
                <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
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
                'Flock Statistics' => [
                    ['label'=>'Chicks','value'=>$chicks,'icon'=>'🐤'],
                    ['label'=>'Hens','value'=>$hens,'icon'=>'🐔'],
                    ['label'=>'Birds','value'=>$birds,'icon'=>'🐥'],
                    ['label'=>'Mortality %','value'=>number_format($mortalityRate,2),'icon'=>'⚰️'],
                ],
                'Production' => [
                    ['label'=>'Eggs','value'=>$metrics['egg_crates'],'icon'=>'🥚'],
                    ['label'=>'Feed (kg)','value'=>$metrics['feed_kg'],'icon'=>'🌾'],
                    ['label'=>'FCR','value'=>$fcr,'icon'=>'⚖️'],
                ],
                'Operations' => [
                    ['label'=>'Employees','value'=>$employees,'icon'=>'👨‍🌾'],
                    ['label'=>'Payroll','value'=>number_format($payroll,2),'icon'=>'💵'],
                    ['label'=>'Sales','value'=>$metrics['sales'],'icon'=>'🛒'],
                    ['label'=>'Customers','value'=>$metrics['customers'],'icon'=>'👥'],
                    ['label'=>'Med Bought','value'=>$metrics['medicine_buy'],'icon'=>'💊'],
                    ['label'=>'Med Used','value'=>$metrics['medicine_use'],'icon'=>'🩺'],
                ],
            ];
        @endphp
        @foreach($groupedKpis as $group => $kpis)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $group }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($kpis as $i => $item)
                        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded shadow hover:shadow-lg transition animate-fadeInUp delay-{{ $i }}00">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded shadow">
                <h4 class="font-medium mb-2 dark:text-gray-200">Egg Trend</h4>
                <div class="relative" style="height: 200px;">
                    <canvas id="eggTrend"></canvas>
                </div>
            </div>
            <div class="bg-white dark:bg-[#1a1a3a] p-4 rounded shadow">
                <h4 class="font-medium mb-2 dark:text-gray-200">Feed Trend</h4>
                <div class="relative" style="height: 200px;">
                    <canvas id="feedTrend"></canvas>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const eggCtx = document.getElementById('eggTrend').getContext('2d');
    new Chart(eggCtx, {
        type: 'line',
        data: {
            labels: @json($eggTrend->pluck('date')),
            datasets: [{
                label: 'Egg Crates',
                data: @json($eggTrend->pluck('value')),
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    const feedCtx = document.getElementById('feedTrend').getContext('2d');
    new Chart(feedCtx, {
        type: 'line',
        data: {
            labels: @json($feedTrend->pluck('date')),
            datasets: [{
                label: 'Feed (kg)',
                data: @json($feedTrend->pluck('value')),
                fill: false,
                borderColor: 'rgba(255, 99, 132, 1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection