@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Health Checks</h2>
        <a href="{{ route('health-checks.create') }}" 
           class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                  dark:bg-blue-500 dark:hover:bg-blue-600 transition">
            ➕ Log New Health Check
        </a>
    </section>

    <!-- Summary Card -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow flex flex-col items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Health Checks</span>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($healthChecks->total(), 0) }}</p>
                <span class="text-gray-600 dark:text-gray-300">Records</span>
            </div>
        </div>
    </section>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-2xl border border-green-200 dark:border-green-700">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Health Checks Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Health Check Records</h3>

            @if ($healthChecks->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No health checks found yet.</p>
                    <a href="{{ route('health-checks.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 
                              dark:bg-blue-500 dark:hover:bg-blue-600 transition">
                        ➕ Log Your First Health Check
                    </a>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">ID</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bird Breed</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Symptoms</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Treatment</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Notes</th>
                                <th class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($healthChecks as $healthCheck)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->id }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->bird->breed ?? 'N/A' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->date->format('Y-m-d') }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ ($healthCheck->status === 'healthy' ? 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-200' : 
                                               ($healthCheck->status === 'unhealthy' ? 'bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-200' : 
                                               'bg-yellow-200 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-200')) }}">
                                            {{ ucfirst($healthCheck->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->symptoms ?? 'None' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->treatment ?? 'None' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->notes ?? 'None' }}</td>
                                    <td class="p-4 flex space-x-2">
                                        <a href="{{ route('health-checks.edit', $healthCheck->id) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-lg shadow hover:bg-yellow-600 text-xs transition">
                                           ✏️ Edit
                                        </a>
                                        <form action="{{ route('health-checks.destroy', $healthCheck->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this health check record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 text-xs transition">
                                                🗑 Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($healthChecks->hasPages())
                    <div class="mt-6 flex justify-end">
                        {{ $healthChecks->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection