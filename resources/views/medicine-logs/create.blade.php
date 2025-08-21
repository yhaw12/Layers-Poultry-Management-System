@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Farm Dashboard</h1>

    <!-- Date Range Filter -->
    <form method="GET" action="{{ route('dashboard.index') }}" class="mb-6 bg-white p-4 rounded-lg shadow">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $start }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $end }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Filter</button>
        </div>
    </form>

    <!-- Alerts -->
    @if($alerts->count())
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Alerts</h2>
            <ul class="space-y-2">
                @foreach($alerts as $alert)
                    <li class="p-3 bg-yellow-100 rounded-md">{{ $alert->message }} <span class="text-sm text-gray-500">({{ $alert->created_at->diffForHumans() }})</span></li>
                @endforeach
            </ul>
            {{ $alerts->links() }}
        </div>
    @endif

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Total Birds</h3>
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($totalBirds) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Egg Crates</h3>
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($eggCrates) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Total Sales</h3>
            <p class="text-2xl font-bold text-indigo-600">${{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Profit</h3>
            <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($profit, 2) }}</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Egg Production Trend -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Egg Production Trend</h2>
            <canvas id="eggProductionChart" height="100"></canvas>
        </div>

        <!-- Sales Comparison (Egg vs Bird) -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Sales Comparison</h2>
            <canvas id="salesComparisonChart" height="100"></canvas>
        </div>

        <!-- Profit Trend -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Profit Trend</h2>
            <canvas id="profitTrendChart" height="100"></canvas>
        </div>

        <!-- Mortality Trend -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Mortality Trend</h2>
            <canvas id="mortalityTrendChart" height="100"></canvas>
        </div>
    </div>

    <!-- Invoice Statuses -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Invoice Statuses</h2>
        <canvas id="invoiceStatusChart" height="100"></canvas>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Activities</h2>
        <ul class="space-y-2">
            @foreach($recentActivities as $activity)
                <li>{{ $activity->user_name }} {{ $activity->action }} <span class="text-sm text-gray-500">({{ $activity->created_at->diffForHumans() }})</span></li>
            @endforeach
        </ul>
    </div>

    <!-- Recent Sales -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Sales</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentSales as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $sale->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $sale->saleable_type == App\Models\Egg::class ? 'Egg' : 'Bird' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${{ number_format($sale->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $sale->sale_date->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Export Buttons -->
    <div class="flex space-x-4 mb-6">
        <a href="{{ route('dashboard.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Export CSV</a>
        <a href="{{ route('dashboard.exportPDF') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Export PDF</a>
    </div>
</div>

<!-- Chart.js Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Egg Production Chart
    const eggProductionChart = new Chart(document.getElementById('eggProductionChart'), {
        type: 'line',
        data: {
            labels: @json($eggTrend->pluck('date')),
            datasets: [{
                label: 'Egg Crates',
                data: @json($eggTrend->pluck('value')),
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Crates' } }
            }
        }
    });

    // Sales Comparison Chart
    const salesComparisonChart = new Chart(document.getElementById('salesComparisonChart'), {
        type: 'bar',
        data: {
            labels: @json($salesComparison->pluck('date')),
            datasets: [
                {
                    label: 'Egg Sales',
                    data: @json($salesComparison->pluck('egg_sales')),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)'
                },
                {
                    label: 'Bird Sales',
                    data: @json($salesComparison->pluck('bird_sales')),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Amount ($)' } }
            }
        }
    });

    // Profit Trend Chart
    const profitTrendChart = new Chart(document.getElementById('profitTrendChart'), {
        type: 'line',
        data: {
            labels: @json($profitTrend->pluck('date')),
            datasets: [{
                label: 'Profit',
                data: @json($profitTrend->pluck('value')),
                borderColor: 'rgba(153, 102, 255, 1)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Profit ($)' } }
            }
        }
    });

    // Mortality Trend Chart
    const mortalityTrendChart = new Chart(document.getElementById('mortalityTrendChart'), {
        type: 'line',
        data: {
            labels: @json($mortalityTrend->pluck('date')),
            datasets: [{
                label: 'Mortalities',
                data: @json($mortalityTrend->pluck('value')),
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Quantity' } }
            }
        }
    });

    // Invoice Status Chart
    const invoiceStatusChart = new Chart(document.getElementById('invoiceStatusChart'), {
        type: 'pie',
        data: {
            labels: ['Pending', 'Paid', 'Overdue'],
            datasets: [{
                data: @json($invoiceStatuses),
                backgroundColor: [
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection
