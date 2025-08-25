@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vaccination Logs</h2>
        @can('manage_vaccinations')
            <a href="{{ route('vaccination-logs.create') }}" 
               class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                      dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                ➕ Add Vaccination
            </a>
        @endcan
    </section>

    <!-- Summary Card -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Vaccinations</span>
                <p class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ number_format($logs->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
        </div>
    </section>

    <!-- Error/Success Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-2xl border border-red-200 dark:border-red-700">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Date Filter Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filter Vaccination Logs</h3>
            <form method="GET" action="{{ route('vaccination-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $start ?? now()->subMonths(6)->startOfMonth()->toDateString() }}" 
                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $end ?? now()->endOfMonth()->toDateString() }}" 
                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-[#1a1a3a] dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-400 focus:ring-opacity-50 transition">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition flex items-center justify-center">
                        🔍 Filter Logs
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Vaccination Trend Chart -->
    <!-- Vaccination Logs Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Vaccination Records</h3>

            @if ($logs->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No vaccination logs found yet.</p>
                    @can('manage_vaccinations')
                        <a href="{{ route('vaccination-logs.create') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                                  dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                            ➕ Add Your First Vaccination Log
                        </a>
                    @endcan
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bird</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Vaccine</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date Administered</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Notes</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Next Vaccination</th>
                                @can('manage_vaccinations')
                                    <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->bird ? $log->bird->name : 'Unknown Bird' }}</td>
                                    <td class="p-4 font-semibold text-teal-600 dark:text-teal-400">{{ $log->vaccine_name }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($log->date_administered)->format('Y-m-d') }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->notes ?? 'N/A' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $log->next_vaccination_date ? \Carbon\Carbon::parse($log->next_vaccination_date)->format('Y-m-d') : 'N/A' }}</td>
                                    @can('manage_vaccinations')
                                        <td class="p-4 flex space-x-2">
                                            <a href="{{ route('vaccination-logs.edit', $log->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                               ✏️ Edit
                                            </a>
                                            <form action="{{ route('vaccination-logs.destroy', $log->id) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this vaccination log?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs transition">
                                                    🗑 Delete
                                                </button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($logs->hasPages())
                    <div class="mt-6 flex justify-end">
                        {{ $logs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection