@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Egg Record Details</h2>
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Date Laid</label>
                <p>{{ $egg->date_laid->format('Y-m-d') }}</p>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Pen / Flock</label>
                <p>{{ $egg->pen->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Crates</label>
                <p>{{ number_format($egg->crates, 2) }}</p>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Total Eggs</label>
                <p>{{ $egg->total_eggs }}</p>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Cracked</label>
                <p>{{ $egg->is_cracked ? 'Yes' : 'No' }}</p>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Egg Size</label>
                <p>{{ $egg->egg_size ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 font-semibold">Created By</label>
                <p>{{ $egg->createdBy->name ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('eggs.index') }}"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Back to List
            </a>
        </div>
    </div>
</div>
@endsection