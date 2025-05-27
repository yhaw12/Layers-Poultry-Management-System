@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Egg Record Details</h2>
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow">
        <p><strong>Date Laid:</strong> {{ $egg->date_laid->format('Y-m-d') }}</p>
        <p><strong>Crates:</strong> {{ $egg->crates }}</p>
        <p><strong>Sold Quantity:</strong> {{ $egg->sold_quantity ?? 'N/A' }}</p>
        <p><strong>Sold Date:</strong> {{ $egg->sold_date ? $egg->sold_date->format('Y-m-d') : 'N/A' }}</p>
        <p><strong>Sale Price:</strong> {{ $egg->sale_price ? number_format($egg->sale_price, 2) : 'N/A' }}</p>
        <a href="{{ route('eggs.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Back to List</a>
    </div>
</div>
@endsection