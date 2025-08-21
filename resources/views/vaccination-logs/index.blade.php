@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-teal-500 dark:text-teal-400">Vaccination Logs</h1>
        @can('manage_vaccinations')
            <a href="{{ route('vaccination-logs.create') }}" class="bg-teal-600 text-white px-6 py-2 rounded-full hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-opacity-50 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Vaccination
            </a>
        @endcan
    </div>

    <!-- Error/Success Messages -->
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-fade-in" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Date Filter Form -->
    <form method="GET" action="{{ route('vaccination-logs.index') }}" class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $start ?? now()->subMonths(6)->startOfMonth()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-400 focus:ring-opacity-50">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $end ?? now()->endOfMonth()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-400 focus:ring-opacity-50">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-teal-600 text-white px-4 py-2 rounded-full hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:ring-opacity-50 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Filter Logs
                </button>
            </div>
        </div>
    </form>

    <!-- Vaccination Trend Chart -->
    @if (isset($logs) && $logs->isNotEmpty())
        <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Vaccination Trend</h2>
            <div class="relative h-64">
                <canvas id="vaccinationTrend" class="w-full h-full"></canvas>
            </div>
        </div>
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                console.log('Vaccination Logs:', @json($logs));
                const ctx = document.getElementById('vaccinationTrend')?.getContext('2d');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($logs->pluck('date_administered')->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))),
                            datasets: [{
                                label: 'Vaccinations Administered',
                                data: @json($logs->groupBy('date_administered')->map->count()),
                                borderColor: '#14b8a6',
                                backgroundColor: 'rgba(20, 184, 166, 0.2)',
                                fill: true,
                                tension: 0.4,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'top' }, title: { display: true, text: 'Vaccination Trend' } },
                            scales: { y: { beginAtZero: true, title: { display: true, text: 'Number of Vaccinations' } }, x: { title: { display: true, text: 'Date' } } }
                        }
                    });
                } else {
                    console.error('Canvas context not found for vaccinationTrend');
                }
            });
        </script>
    @else
        <div class="mb-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
            <p class="text-gray-500 dark:text-gray-400">No vaccination data available for the chart.</p>
        </div>
    @endif

    <!-- Vaccination Logs Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($logs as $log)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md hover:shadow-lg transition relative">
                <div class="absolute top-4 right-4">
                    <svg class="w-6 h-6 text-teal-500 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $log->bird ? $log->bird->name : 'Unknown Bird' }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Vaccine:</strong> {{ $log->vaccine_name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Date:</strong> {{ \Carbon\Carbon::parse($log->date_administered)->format('Y-m-d') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Notes:</strong> {{ $log->notes ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Next Vaccination:</strong> {{ $log->next_vaccination_date ? \Carbon\Carbon::parse($log->next_vaccination_date)->format('Y-m-d') : 'N/A' }}</p>
                @can('manage_vaccinations')
                    <div class="mt-4 flex gap-4">
                        <a href="{{ route('vaccination-logs.edit', $log->id) }}" class="text-teal-500 hover:text-teal-600 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </a>
                        <form action="{{ route('vaccination-logs.destroy', $log->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-600 font-medium flex items-center" onclick="return confirm('Are you sure you want to delete this log?')">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4"></path></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md text-center">
                <p class="text-gray-500 dark:text-gray-400">No vaccination logs found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($logs->hasPages())
        <div class="mt-8">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
