@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
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

    <!-- Sidebar and Content -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar -->
        <aside class="w-full md:w-1/4 bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Report Types</h3>
            <nav class="space-y-2">
                @foreach (['weekly', 'monthly', 'custom', 'profitability'] as $tab)
                    <button data-tab="{{ $tab }}"
                            class="tab-btn w-full text-left px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 {{ $reportType === $tab ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                            role="tab" aria-selected="{{ $reportType === $tab ? 'true' : 'false' }}"
                            aria-controls="{{ $tab }}-panel">{{ $tab === 'custom' ? 'Analytics' : ucfirst($tab) }}</button>
                @endforeach
            </nav>
        </aside>

        <!-- Report Content -->
        <main class="w-full md:w-3/4">
            <div id="report-content" class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
                <!-- Weekly Report -->
                <div id="weekly-panel" class="tab-panel {{ $reportType === 'weekly' ? '' : 'hidden' }}">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Weekly Egg Report</h2>
                    @if (empty($data['weekly']) || $data['weekly']->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400 text-center py-6">No data found.</p>
                    @else
                        <canvas id="weekly-chart" class="h-32 mb-6"></canvas>
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
                        <p class="text-gray-600 dark:text-gray-400 text-center py-6">No data found.</p>
                    @else
                        <canvas id="monthly-chart" class="h-32 mb-6"></canvas>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ $validated['start_date'] ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ $validated['end_date'] ?? '' }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metrics</label>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="eggs" {{ in_array('eggs', $validated['metrics'] ?? []) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Eggs</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="sales" {{ in_array('sales', $validated['metrics'] ?? []) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Sales</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="metrics[]" value="expenses" {{ in_array('expenses', $validated['metrics'] ?? []) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Expenses</span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Generate</button>
                    </form>
                    @if (!empty($data['eggs']) || !empty($data['sales']) || !empty($data['expenses']))
                        <div class="mt-8">
                            @if (!empty($data['eggs']))
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Eggs</h4>
                                <canvas id="eggs-chart" class="h-32 mb-6"></canvas>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Laid</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Crates</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($data['eggs'] as $egg)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $egg->date_laid }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $egg->crates }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if (!empty($data['sales']))
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Sales</h4>
                                <canvas id="sales-chart" class="h-32 mb-6"></canvas>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($data['sales'] as $sale)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->sale_date }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->customer->name ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($sale->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if (!empty($data['expenses']))
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Expenses</h4>
                                <canvas id="expenses-chart" class="h-32 mb-6"></canvas>
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Profitability Report -->
                <div id="profitability-panel" class="tab-panel {{ $reportType === 'profitability' ? '' : 'hidden' }}">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Profitability Report</h2>
                    @if (empty($data['profitability']) || $data['profitability']->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400 text-center py-6">No data found.</p>
                    @else
                        <canvas id="profitability-chart" class="h-32 mb-6"></canvas>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
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
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->breed }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->sales, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->feed_cost, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($row->expenses, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap {{ $row->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($row->profit, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tab Navigation
        const tabs = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.tab-panel');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabType = tab.getAttribute('data-tab');
                const url = new URL(window.location);
                url.searchParams.set('type', tabType);
                window.history.pushState({}, '', url);
                tabs.forEach(t => {
                    t.classList.remove('bg-blue-600', 'text-white');
                    t.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-800');
                    t.setAttribute('aria-selected', 'false');
                });
                tab.classList.add('bg-blue-600', 'text-white');
                tab.setAttribute('aria-selected', 'true');
                panels.forEach(panel => {
                    panel.classList.add('hidden');
                    if (panel.id === `${tabType}-panel`) panel.classList.remove('hidden');
                });
            });
        });

        // Export Functionality
        const exportBtn = document.getElementById('export-btn');
        const exportDropdown = document.getElementById('export-dropdown');
        const exportOptions = exportDropdown.querySelectorAll('button');
        exportBtn.addEventListener('click', () => exportDropdown.classList.toggle('hidden'));
        exportOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                const format = option.getAttribute('data-format');
                const currentType = new URLSearchParams(window.location.search).get('type') || 'weekly';
                if (currentType === 'custom') {
                    const form = document.getElementById('custom-report-form');
                    if (form.checkValidity()) {
                        const formData = new FormData(form);
                        formData.set('format', format);
                        const url = '{{ route('reports.custom') }}?' + new URLSearchParams(formData).toString();
                        window.location.href = url;
                    } else {
                        alert('Please fill all required fields.');
                        exportDropdown.classList.add('hidden');
                    }
                } else {
                    window.location.href = `{{ route('reports.export') }}?type=${currentType}&format=${format}`;
                }
            });
        });
        document.addEventListener('click', (e) => {
            if (!exportBtn.contains(e.target) && !exportDropdown.contains(e.target)) {
                exportDropdown.classList.add('hidden');
            }
        });

        // Charts
        @if ($reportType === 'weekly' && !empty($data['weekly']) && $data['weekly']->isNotEmpty())
            new Chart(document.getElementById('weekly-chart'), {
                type: 'line',
                data: {
                    labels: @json($data['weekly']->map(fn($row) => "Week {$row->week}, {$row->year}")),
                    datasets: [{ label: 'Total Eggs', data: @json($data['weekly']->pluck('total')), borderColor: '#10b981', fill: true }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        @endif

        @if ($reportType === 'monthly' && !empty($data['monthly']) && $data['monthly']->isNotEmpty())
            new Chart(document.getElementById('monthly-chart'), {
                type: 'bar',
                data: {
                    labels: @json($data['monthly']->map(fn($row) => \Carbon\Carbon::create()->month($row->month_num)->format('F') . " {$row->year}")),
                    datasets: [{ label: 'Total Eggs', data: @json($data['monthly']->pluck('total')), backgroundColor: '#8b5cf6' }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        @endif

        @if ($reportType === 'custom' && !empty($data['eggs']) && $data['eggs']->isNotEmpty())
            new Chart(document.getElementById('eggs-chart'), {
                type: 'line',
                data: {
                    labels: @json($data['eggs']->pluck('date_laid')),
                    datasets: [{ label: 'Egg Crates', data: @json($data['eggs']->pluck('crates')), borderColor: '#f59e0b', fill: true }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        @endif

        @if ($reportType === 'custom' && !empty($data['sales']) && $data['sales']->isNotEmpty())
            new Chart(document.getElementById('sales-chart'), {
                type: 'bar',
                data: {
                    labels: @json($data['sales']->pluck('sale_date')),
                    datasets: [{ label: 'Total Sales ($)', data: @json($data['sales']->pluck('total_amount')), backgroundColor: '#ef4444' }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        @endif

        @if ($reportType === 'custom' && !empty($data['expenses']) && $data['expenses']->isNotEmpty())
            new Chart(document.getElementById('expenses-chart'), {
                type: 'bar',
                data: {
                    labels: @json($data['expenses']->pluck('date')),
                    datasets: [{ label: 'Total Expenses ($)', data: @json($data['expenses']->pluck('amount')), backgroundColor: '#f97316' }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        @endif

        @if ($reportType === 'profitability' && !empty($data['profitability']) && $data['profitability']->isNotEmpty())
            new Chart(document.getElementById('profitability-chart'), {
                type: 'bar',
                data: {
                    labels: @json($data['profitability']->pluck('breed')),
                    datasets: [
                        { label: 'Sales ($)', data: @json($data['profitability']->pluck('sales')), backgroundColor: '#10b981' },
                        { label: 'Feed Cost ($)', data: @json($data['profitability']->pluck('feed_cost')), backgroundColor: '#f97316' },
                        { label: 'Expenses ($)', data: @json($data['profitability']->pluck('expenses')), backgroundColor: '#ef4444' },
                        { label: 'Profit ($)', data: @json($data['profitability']->pluck('profit')), backgroundColor: '#3b82f6' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        @endif
    });
</script>
@endsection
