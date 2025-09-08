@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <section class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">🐓 Feed Consumption</h2>
            <p class="text-gray-600 dark:text-gray-400">Consumption history and total usage.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('feed.consumption.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                ➕ Record New Consumption
            </a>
        </div>
    </section>

    <!-- KPI -->
    <section>
        <div class="bg-blue-50 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 text-center shadow-md max-w-lg">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wide">Total Consumed</h4>
            <p class="text-3xl font-bold text-blue-700 dark:text-blue-400 mt-2">{{ number_format($totalConsumed ?? 0, 2) }} kg</p>
        </div>
    </section>

    <!-- History -->
    <section>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">📊 Consumption History</h3>

        @if (empty($consumptions) || $consumptions->isEmpty())
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md text-center">
                <p class="text-gray-600 dark:text-gray-400">No records yet. Click "Record New Consumption" to add your first entry.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
                <table class="w-full border-collapse bg-white dark:bg-[#1a1a3a]">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0">
                        <tr>
                            <th class="p-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Feed</th>
                            <th class="p-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Date</th>
                            <th class="p-3 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Quantity (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumptions as $index => $consumption)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800/40' : 'bg-white dark:bg-[#1a1a3a]' }} hover:bg-blue-50 dark:hover:bg-blue-900/30 transition">
                                <td class="p-3 text-sm">{{ optional($consumption->feed)->type ?? 'Unknown' }}</td>
                                <td class="p-3 text-sm">{{ optional($consumption->date)->format('Y-m-d') ?? \Carbon\Carbon::parse($consumption->date)->format('Y-m-d') }}</td>
                                <td class="p-3 text-sm font-medium">{{ number_format($consumption->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-between items-center">
                @if ($consumptions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="flex items-center space-x-2">
                        @if ($consumptions->onFirstPage())
                            <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">⬅ Previous</span>
                        @else
                            <a href="{{ $consumptions->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-gray-300 text-gray-800 hover:bg-gray-400">⬅ Previous</a>
                        @endif

                        <span class="text-sm text-gray-600 dark:text-gray-400">Page {{ $consumptions->currentPage() }} of {{ $consumptions->lastPage() }}</span>

                        @if ($consumptions->hasMorePages())
                            <a href="{{ $consumptions->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Next ➡</a>
                        @else
                            <span class="px-4 py-2 rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">Next ➡</span>
                        @endif
                    </div>
                @else
                    <div class="text-sm text-gray-600 dark:text-gray-400">Showing {{ count($consumptions) }} records</div>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
