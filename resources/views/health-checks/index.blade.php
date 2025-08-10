{{-- @extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Health Checks</h2>
            <a href="{{ route('health-checks.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Log New Health Check
            </a>
        </div>
    </section>

    <!-- Health Checks Table -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if ($healthChecks->isEmpty())
                <p class="text-gray-600 dark:text-gray-400">No health checks found.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="p-3 text-gray-700 dark:text-gray-200">ID</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Bird Breed</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Date</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Status</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Symptoms</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Treatment</th>
                                <th class="p-3 text-gray-700 dark:text-gray-200">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($healthChecks as $healthCheck)
                                <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    <td class="p-3">{{ $healthCheck->id }}</td>
                                    <td class="p-3">{{ $healthCheck->bird->breed ?? 'N/A' }}</td>
                                    <td class="p-3">{{ $healthCheck->date->format('Y-m-d') }}</td>
                                    <td class="p-3">{{ $healthCheck->status }}</td>
                                    <td class="p-3">{{ $healthCheck->symptoms ?? 'None' }}</td>
                                    <td class="p-3">{{ $healthCheck->treatment ?? 'None' }}</td>
                                    <td class="p-3">{{ $healthCheck->notes ?? 'None' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $healthChecks->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection --}}