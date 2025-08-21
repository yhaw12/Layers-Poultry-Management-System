@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-8 p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
        Transaction Details
    </h1>

    <div class="space-y-4">
        <div>
            <strong class="text-gray-700 dark:text-gray-300">Date:</strong>
            <span>{{ $transaction->date->format('Y-m-d') }}</span>
        </div>

        <div>
            <strong class="text-gray-700 dark:text-gray-300">Amount:</strong>
            <span>{{ number_format($transaction->amount, 2) }}</span>
        </div>

        <div>
            <strong class="text-gray-700 dark:text-gray-300">Type:</strong>
            <span class="capitalize">{{ $transaction->type }}</span>
        </div>

        <div>
            <strong class="text-gray-700 dark:text-gray-300">Customer:</strong>
            <span>{{ $transaction->customer->name ?? 'N/A' }}</span>
        </div>

        <div>
            <strong class="text-gray-700 dark:text-gray-300">Description:</strong>
            <p class="mt-1">{{ $transaction->description ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="mt-6 flex space-x-4">
        <form method="POST" action="{{ route('transactions.approve', $transaction->id) }}">
            @csrf
            <button type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Approve
            </button>
        </form>

        <form method="POST" action="{{ route('transactions.decline', $transaction->id) }}">
            @csrf
            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Decline
            </button>
        </form>
    </div>
</div>
@endsection
