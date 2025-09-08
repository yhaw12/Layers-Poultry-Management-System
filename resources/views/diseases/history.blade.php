@extends('layouts.app')

@section('title', 'Disease History: {{ $disease->name }}')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Disease History: {{ $disease->name }}</h2>
        <a href="{{ route('diseases.index') }}" 
           class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 transition" 
           aria-label="Back to diseases">
            <span class="mr-2" aria-hidden="true">⬅️</span> Back to Diseases
        </a>
    </section>

    <!-- History Table -->
    <section>
        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Health Check Records</h3>
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
            @if ($history->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">No health check records found for this disease.</p>
                </div>
            @else
                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-x-auto rounded-lg">
                    <table class="w-full border-collapse rounded-lg overflow-hidden text-sm">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Bird</th>
                                <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Date</th>
                                <th scope="col" class="p-4 text-left font-medium text-gray-700 dark:text-gray-200 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach ($history as $healthCheck)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->bird->name ?? 'Unknown' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->date ? $healthCheck->date->format('Y-m-d') : 'N/A' }}</td>
                                    <td class="p-4 text-gray-700 dark:text-gray-300">{{ $healthCheck->details ?? 'No details' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Mobile Card Layout -->
                <div class="sm:hidden space-y-4">
                    @foreach ($history as $healthCheck)
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow-sm">
                            <div class="grid grid-cols-1 gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <div><strong>Bird:</strong> {{ $healthCheck->bird->name ?? 'Unknown' }}</div>
                                <div><strong>Date:</strong> {{ $healthCheck->date ? $healthCheck->date->format('Y-m-d') : 'N/A' }}</div>
                                <div><strong>Details:</strong> {{ $healthCheck->details ?? 'No details' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center">
                    @if ($history->hasPages())
                        <div class="flex space-x-2">
                            <a href="{{ $history->previousPageUrl() }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ $history->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}"
                               aria-label="Previous page" {{ $history->onFirstPage() ? 'disabled' : '' }}>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </a>
                            <a href="{{ $history->nextPageUrl() }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition {{ !$history->hasMorePages() ? 'opacity-50 cursor-not-allowed' : '' }}"
                               aria-label="Next page" {{ !$history->hasMorePages() ? 'disabled' : '' }}>
                                Next
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Page {{ $history->currentPage() }} of {{ $history->lastPage() }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>
@endsection