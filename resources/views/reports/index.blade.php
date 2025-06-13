@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Farm Reports</h1>

        <!-- Tabs Navigation -->
        <div class="mb-8">
            <nav class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700" role="tablist">
                @foreach (['daily', 'weekly', 'monthly', 'custom', 'profitability'] as $tab)
                    <a href="{{ route('reports.index', ['type' => $tab]) }}"
                       class="px-4 py-2 text-sm font-medium rounded-t-lg transition-colors duration-200 {{ $reportType === $tab ? 'bg-blue-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                       role="tab" aria-selected="{{ $reportType === $tab ? 'true' : 'false' }}"
                       aria-controls="{{ $tab }}-panel">{{ ucfirst($tab) }}</a>
                @endforeach
            </nav>
        </div>

        <!-- Report Content -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <!-- Daily Report -->
            @if ($reportType === 'daily')
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Daily Egg Report</h2>
                @if (empty($data['daily']) || $data['daily']->isEmpty())
                    <div class="text-gray-600 dark:text-gray-400 text-center py-6">
                        No egg data found for the last 7 days.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <th class="border px-4 py-2 text-left font-medium">Date</th>
                                    <th class="border px-4 py-2 text-left font-medium">Total Eggs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['daily'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->date }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            <!-- Weekly Report -->
            @if ($reportType === 'weekly')
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Weekly Egg Report</h2>
                @if (empty($data['weekly']) || $data['weekly']->isEmpty())
                    <div class="text-gray-600 dark:text-gray-400 text-center py-6">
                        No egg data found for the last 8 weeks.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <th class="border px-4 py-2 text-left font-medium">Year</th>
                                    <th class="border px-4 py-2 text-left font-medium">Week</th>
                                    <th class="border px-4 py-2 text-left font-medium">Total Eggs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['weekly'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->year }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->week }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            <!-- Monthly Report -->
            @if ($reportType === 'monthly')
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Monthly Egg Report</h2>
                @if (empty($data['monthly']) || $data['monthly']->isEmpty())
                    <div class="text-gray-600 dark:text-gray-400 text-center py-6">
                        No egg data found for the last 6 months.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <th class="border px-4 py-2 text-left font-medium">Year</th>
                                    <th class="border px-4 py-2 text-left font-medium">Month</th>
                                    <th class="border px-4 py-2 text-left font-medium">Total Eggs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['monthly'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->year }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->month }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif

            <!-- Custom Report -->
            @if ($reportType === 'custom')
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Custom Report</h2>
                <form id="custom-report-form" method="POST" action="{{ route('reports.custom') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="custom">

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        @error('start_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                        <input type="date" id="end_date" name="end_date" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        @error('end_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metrics</label>
                        <div class="mt-2 space-x-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="metrics[]" value="eggs" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Eggs</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="metrics[]" value="sales" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Sales</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="metrics[]" value="expenses" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Expenses</span>
                            </label>
                        </div>
                        @error('metrics')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Format</label>
                        <select id="format" name="format" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                                id="submit-btn">
                            <span>Generate Report</span>
                            <svg id="loading-spinner" class="hidden w-5 h-5 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-8 8 8 8 0 01-8-8z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                @if (!empty($data['eggs']) || !empty($data['sales']) || !empty($data['expenses']))
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">Custom Report Results</h2>
                    @if (!empty($data['eggs']))
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">Eggs</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <th class="border px-4 py-2 text-left font-medium">Date Laid</th>
                                        <th class="border px-4 py-2 text-left font-medium">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['eggs'] as $egg)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $egg->date_laid }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $egg->quantity ?? 1 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (!empty($data['sales']))
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mt-6 mb-2">Sales</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <th class="border px-4 py-2 text-left font-medium">Date</th>
                                        <th class="border px-4 py-2 text-left font-medium">Customer</th>
                                        <th class="border px-4 py-2 text-left font-medium">Item</th>
                                        <th class="border px-4 py-2 text-left font-medium">Quantity</th>
                                        <th class="border px-4 py-2 text-left font-medium">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['sales'] as $sale)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $sale->sale_date }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $sale->customer->name ?? 'N/A' }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $sale->quantity }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">${{ number_format($sale->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (!empty($data['expenses']))
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mt-6 mb-2">Expenses</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <th class="border px-4 py-2 text-left font-medium">Date</th>
                                        <th class="border px-4 py-2 text-left font-medium">Description</th>
                                        <th class="border px-4 py-2 text-left font-medium">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['expenses'] as $expense)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $expense->date }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $expense->description ?? 'N/A' }}</td>
                                            <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">${{ number_format($expense->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            @endif

            <!-- Profitability Report -->
            @if ($reportType === 'profitability')
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Profitability Report</h2>
                @if (empty($data['profitability']) || $data['profitability']->isEmpty())
                    <div class="text-gray-600 dark:text-gray-400 text-center py-6">
                        No profitability data found.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <th class="border px-4 py-2 text-left font-medium">Bird ID</th>
                                    <th class="border px-4 py-2 text-left font-medium">Breed</th>
                                    <th class="border px-4 py-2 text-left font-medium">Sales ($)</th>
                                    <th class="border px-4 py-2 text-left font-medium">Feed Cost ($)</th>
                                    <th class="border px-4 py-2 text-left font-medium">Expenses ($)</th>
                                    <th class="border px-4 py-2 text-left font-medium">Profit ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['profitability'] as $row)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row['bird_id'] }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ $row['breed'] }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ number_format($row['sales'], 2) }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ number_format($row['feed_cost'], 2) }}</td>
                                        <td class="border px-4 py-2 text-gray-700 dark:text-gray-300">{{ number_format($row['expenses'], 2) }}</td>
                                        <td class="border px-4 py-2 {{ $row['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($row['profit'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('custom-report-form');
            const submitBtn = document.getElementById('submit-btn');
            const loadingSpinner = document.getElementById('loading-spinner');

            if (form && submitBtn && loadingSpinner) {
                form.addEventListener('submit', (e) => {
                    // Check if form is valid
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        alert('Please fill all required fields.');
                        return;
                    }
                    submitBtn.disabled = true;
                    loadingSpinner.classList.remove('hidden');
                    submitBtn.querySelector('span').textContent = 'Generating...';

                    // Reset button after 10 seconds if no response (timeout fallback)
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

            // Persist active tab in URL
            const tabs = document.querySelectorAll('nav a[role="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    tabs.forEach(t => t.setAttribute('aria-selected', 'false'));
                    tab.setAttribute('aria-selected', 'true');
                });
            });
        });
    </script>
@endsection