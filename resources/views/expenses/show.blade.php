@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23]">
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Expense Details</h1>
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-lg shadow-md">
        <p class="text-gray-800 dark:text-gray-200"><strong>Category:</strong> {{ $expense->category }}</p>
        <p class="text-gray-800 dark:text-gray-200"><strong>Description:</strong> {{ $expense->description ?? 'N/A' }}</p>
        <p class="text-gray-800 dark:text-gray-200"><strong>Amount:</strong> ${{ number_format($expense->amount, 2) }}</p>
        <p class="text-gray-800 dark:text-gray-200"><strong>Date:</strong> {{ $expense->date->format('Y-m-d') }}</p>
        <a href="{{ route('expenses.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mt-4 inline-block">Back to List</a>
    </div>
</div>
@endsection