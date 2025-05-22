@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Date Filter -->
    <form method="GET" class="mb-8 bg-white p-6 rounded shadow">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1">
                <label class="block text-gray-700">Start Date</label>
                <input type="date" name="start_date" value="{{ $start }}" class="w-full border rounded p-2">
            </div>
            <div class="flex-1">
                <label class="block text-gray-700">End Date</label>
                <input type="date" name="end_date" value="{{ $end }}" class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded">Filter</button>
        </div>
    </form>

    <!-- Summary Cards -->
    @php
        $cards = [
            ['label'=>'Expenses', 'value'=>$totalExpenses, 'icon'=>'💸', 'color'=>'red'],
            ['label'=>'Income', 'value'=>$totalIncome,  'icon'=>'💰', 'color'=>'green'],
            ['label'=>'Profit',  'value'=>$profit,       'icon'=>'📈', 'color'=>$profit>=0?'green':'red'],
        ];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($cards as $card)
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">{{ $card['label'] }}</h2>
                    <span class="text-2xl text-{{ $card['color'] }}-500">{{ $card['icon'] }}</span>
                </div>
                <p class="text-3xl font-bold text-{{ $card['color'] }}-600 mt-4">
                    {{ number_format($card['value'],2) }}
                </p>
            </div>
        @endforeach
    </div>

    <!-- KPI Grid -->
    @php
        $kpis = [
            ['label'=>'Chicks','value'=>$chicks,'icon'=>'🐤'],
            ['label'=>'Hens','value'=>$hens,'icon'=>'🐔'],
            ['label'=>'Birds','value'=>$birds,'icon'=>'🐥'],
            ['label'=>'Eggs','value'=>$metrics['egg_crates'],'icon'=>'🥚'],
            ['label'=>'Feed (kg)','value'=>$metrics['feed_kg'],'icon'=>'🌾'],
            ['label'=>'Mortality %','value'=>number_format($mortalityRate,2),'icon'=>'⚰️'],
            ['label'=>'FCR','value'=>$fcr,'icon'=>'⚖️'],
            ['label'=>'Employees','value'=>$employees,'icon'=>'👨‍🌾'],
            ['label'=>'Payroll','value'=>number_format($payroll,2),'icon'=>'💵'],
            ['label'=>'Sales','value'=>$metrics['sales'],'icon'=>'🛒'],
            ['label'=>'Customers','value'=>$metrics['customers'],'icon'=>'👥'],
            ['label'=>'Med Bought','value'=>$metrics['medicine_buy'],'icon'=>'💊'],
            ['label'=>'Med Used','value'=>$metrics['medicine_use'],'icon'=>'🩺'],
        ];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach($kpis as $i => $item)
            <div class="bg-white p-6 rounded shadow hover:shadow-lg transition animate-fadeInUp delay-{{ $i }}00">
                <div class="flex items-center justify-between">
                    <h3 class="text-gray-700 font-medium">{{ $item['label'] }}</h3>
                    <span class="text-2xl">{{ $item['icon'] }}</span>
                </div>
                <p class="text-2xl font-bold text-blue-600 mt-2">{{ $item['value'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Trend Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <div class="bg-white p-4 rounded shadow">
            <h4 class="font-medium mb-2">Egg Trend</h4>
            <canvas id="eggTrend"></canvas>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h4 class="font-medium mb-2">Feed Trend</h4>
            <canvas id="feedTrend"></canvas>
        </div>
    </div>
</div>
@endsection