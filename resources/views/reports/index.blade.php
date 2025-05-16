@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header with Export Button -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Farm Reports</h1>
            <div class="relative">
                <button id="export-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Export Report
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                    <button data-format="pdf" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">PDF</button>
                    <button data-format="excel" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Excel</button>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8">
            <nav class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700" role="tablist">
                @foreach (['weekly', 'monthly', 'custom', 'profitability'] as $tab)
                    <button data-tab="{{ $tab }}"
                            class="tab-btn px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 {{ $reportType === $tab ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                            role="tab" aria-selected="{{ $reportType === $tab ? 'true' : 'false' }}"
                            aria-controls="{{ $tab }}-panel">{{ $tab === 'custom' ? 'Analytics' : ucfirst($tab) }}</button>
                @endforeach
            </nav>
        </div>

        <!-- Report Panels -->
        <div id="report-content" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <!-- Weekly Report -->
            <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Weekly Egg Report</h2>
                @if (empty($data['weekly']) || $data['weekly']->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No egg data found for the last 8 weeks.</p>
                @else
                    <div class="mb-6 h-64">
                        <canvas id="weekly-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('weekly-chart'), {
                                type: 'line',
                                data: {
                                    labels: @json($data['weekly']->map(fn($row) => "Week {$row->week}, {$row->year}")),
                                    datasets: [{
                                        label: 'Total Eggs',
                                        data: @json($data['weekly']->pluck('total')),
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: { display: true, text: 'Eggs' }
                                        },
                                        x: {
                                            title: { display: true, text: 'Week' }
                                        }
                                    },
                                    plugins: {
                                        legend: { position: 'top' }
                                    }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Week</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Eggs</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['weekly'] as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->week }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Monthly Report -->
            <div id="monthly-panel" class="tab-panel {{ $reportType === 'monthly' ? '' : 'hidden' }}">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Monthly Egg Report</h2>
                @if (empty($data['monthly']) || $data['monthly']->isEmpty())
                    <p class="text-gray-600 dark:text-gray-400 text-center py-6">No egg data found for the last 6 months.</p>
                @else
                    <div class="mb-6 h-64">
                        <canvas id="monthly-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('monthly-chart'), {
                                type: 'bar',
                                data: {
                                    labels: @json($data['monthly']->map(fn($row) => \Carbon\Carbon::create()->month($row->month_num)->format('F') . " {$row->year}")),
                                    datasets: [{
                                        label: 'Total Eggs',
                                        data: @json($data['monthly']->pluck('total')),
                                        backgroundColor: '#8b5cf6',
                                        borderColor: '#7c3aed',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            title: { display: true, text: 'Eggs' }
                                        },
                                        x: {
                                            title: { display: true, text: 'Month' }
                                        }
                                    },
                                    plugins: {
                                        legend: { position: 'top' }
                                    }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Eggs</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['monthly'] as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

<!-- Analytics (Custom) Report -->
<div id="custom-panel" class="tab-panel {{ $reportType === 'custom' ? '' : 'hidden' }}">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Analytics Report</h2>
    <form id="custom-report-form" method="GET" action="{{ route('reports.custom') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="type" value="custom">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
            <input type="date" id="start_date" name="start_date" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            @error('start_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
            <input type="date" id="end_date" name="end_date" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 shadow-sm">
            @error('end_date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metrics</label>
            <div class="mt-1 space-y-2">
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="metrics[]" value="eggs" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Eggs</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="metrics[]" value="sales" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Sales</span>
                    </label>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="metrics[]" value="expenses" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-gray-700 dark:text-gray-300">Expenses</span>
                    </label>
                </div>
            </div>
            @error('metrics')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50" id="submit-btn">
                <span>Generate Report</span>
                <svg id="loading-spinner" class="hidden w-5 h-5 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                </svg>
            </button>
        </div>
    </form>
    @if (!empty($data['eggs']) || !empty($data['sales']) || !empty($data['expenses']))
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Analytics Report Results</h3>
            @if (!empty($data['eggs']))
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Eggs</h4>
                    <div class="h-64">
                        <canvas id="eggs-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('eggs-chart'), {
                                type: 'line',
                                data: {
                                    labels: @json($data['eggs']->pluck('date_laid')),
                                    datasets: [{
                                        label: 'Egg Quantity',
                                        data: @json($data['eggs']->pluck('quantity')->map(fn($q) => $q ?? 1)),
                                        borderColor: '#f59e0b',
                                        backgroundColor: 'rgba(245, 158, 11, 0.2)',
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: { beginAtZero: true, title: { display: true, text: 'Quantity' } },
                                        x: { title: { display: true, text: 'Date' } }
                                    },
                                    plugins: { legend: { position: 'top' } }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Laid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['eggs'] as $egg)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $egg->date_laid }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $egg->quantity ?? 1 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (!empty($data['sales']))
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Sales</h4>
                    <div class="h-64">
                        <canvas id="sales-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('sales-chart'), {
                                type: 'bar',
                                data: {
                                    labels: @json($data['sales']->pluck('sale_date')),
                                    datasets: [{
                                        label: 'Total Sales ($)',
                                        data: @json($data['sales']->pluck('total_amount')),
                                        backgroundColor: '#ef4444',
                                        borderColor: '#dc2626',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                                        x: { title: { display: true, text: 'Date' } }
                                    },
                                    plugins: { legend: { position: 'top' } }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['sales'] as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->sale_date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->customer->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @if (!empty($data['expenses']))
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Expenses</h4>
                    <div class="h-64">
                        <canvas id="expenses-chart"></canvas>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new Chart(document.getElementById('expenses-chart'), {
                                type: 'bar',
                                data: {
                                    labels: @json($data['expenses']->pluck('date')),
                                    datasets: [{
                                        label: 'Total Expenses ($)',
                                        data: @json($data['expenses']->pluck('amount')),
                                        backgroundColor: '#f97316',
                                        borderColor: '#ea580c',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                                        x: { title: { display: true, text: 'Date' } }
                                    },
                                    plugins: { legend: { position: 'top' } }
                                }
                            });
                        });
                    </script>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-4">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($data['expenses'] as $expense)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $expense->date }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $expense->description ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">${{ number_format($expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Profitability Report -->
<div id="profitability-panel" class="tab-panel {{ $reportType === 'profitability' ? '' : 'hidden' }}">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Profitability Report</h2>
    @if (empty($data['profitability']) || $data['profitability']->isEmpty())
        <p class="text-gray-600 dark:text-gray-400 text-center py-6">No profitability data found.</p>
    @else
        <div class="mb-6 h-64">
            <canvas id="profitability-chart"></canvas>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Chart(document.getElementById('profitability-chart'), {
                    type: 'bar',
                    data: {
                        labels: @json($data['profitability']->pluck('breed')),
                        datasets: [
                            {
                                label: 'Sales ($)',
                                data: @json($data['profitability']->pluck('sales')),
                                backgroundColor: '#10b981'
                            },
                            {
                                label: 'Feed Cost ($)',
                                data: @json($data['profitability']->pluck('feed_cost')),
                                backgroundColor: '#f97316'
                            },
                            {
                                label: 'Expenses ($)',
                                data: @json($data['profitability']->pluck('expenses')),
                                backgroundColor: '#ef4444'
                            },
                            {
                                label: 'Profit ($)',
                                data: @json($data['profitability']->pluck('profit')),
                                backgroundColor: '#3b82f6'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Amount ($)' } },
                            x: { title: { display: true, text: 'Breed' } }
                        },
                        plugins: { legend: { position: 'top' } }
                    }
                });
            });
        </script>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bird ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Breed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales ($)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Feed Cost ($)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expenses ($)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Profit ($)</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($data['profitability'] as $row)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->bird_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->breed }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->sales, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->feed_cost, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->expenses, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap {{ $row->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($row->profit, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tab-btn');
            const panels = document.querySelectorAll('.tab-panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabType = tab.getAttribute('data-tab');
                    // Update URL
                    const url = new URL(window.location);
                    url.searchParams.set('type', tabType);
                    window.history.pushState({}, '', url);
                    // Update active tab
                    tabs.forEach(t => {
                        t.classList.remove('bg-blue-600', 'text-white');
                        t.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-800');
                        t.setAttribute('aria-selected', 'false');
                    });
                    tab.classList.add('bg-blue-600', 'text-white');
                    tab.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-800');
                    tab.setAttribute('aria-selected', 'true');
                    // Show/hide panels
                    panels.forEach(panel => {
                        panel.classList.add('hidden');
                        if (panel.id === `${tabType}-panel`) {
                            panel.classList.remove('hidden');
                        }
                    });
                });
            });

            // Export Dropdown
            const exportBtn = document.getElementById('export-btn');
            const exportDropdown = document.getElementById('export-dropdown');
            const exportOptions = exportDropdown.querySelectorAll('button');
            exportBtn.addEventListener('click', () => {
                exportDropdown.classList.toggle('hidden');
            });
            exportOptions.forEach(option => {
                option.addEventListener('click', (e) => {
                    e.preventDefault();
                    const format = option.getAttribute('data-format');
                    const currentType = new URLSearchParams(window.location.search).get('type') || 'weekly';
                    let url = `{{ route('reports.export') }}?type=${currentType}&format=${format}`;
                    
                    if (currentType === 'custom') {
                        const form = document.getElementById('custom-report-form');
                        if (form.checkValidity()) {
                            const formData = new FormData(form);
                            formData.set('format', format);
                            formData.set('type', 'custom');
                            url = '{{ route('reports.export') }}?' + new URLSearchParams(formData).toString();
                            window.location.href = url;
                        } else {
                            alert('Please fill all required fields in the analytics report form.');
                            exportDropdown.classList.add('hidden');
                        }
                    } else {
                        window.location.href = url;
                    }
                });
            });
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!exportBtn.contains(e.target) && !exportDropdown.contains(e.target)) {
                    exportDropdown.classList.add('hidden');
                }
            });

            // Custom Report Form Submission
            const form = document.getElementById('custom-report-form');
            const submitBtn = document.getElementById('submit-btn');
            const loadingSpinner = document.getElementById('loading-spinner');
            if (form && submitBtn && loadingSpinner) {
                form.addEventListener('submit', (e) => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        alert('Please fill all required fields.');
                        return;
                    }
                    submitBtn.disabled = true;
                    loadingSpinner.classList.remove('hidden');
                    submitBtn.querySelector('span').textContent = 'Generating...';
                    // Fallback timeout
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.disabled = false;
                            loadingSpinner.classList.add('hidden');
                            submitBtn.querySelector('span').textContent = 'Generate Report';
                            alert('Report generation timed out. Please try again.');
                        }
                    }, 10000);
                });
            }
        });
    </script>
@endsection