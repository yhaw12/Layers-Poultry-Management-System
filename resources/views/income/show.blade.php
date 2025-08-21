@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Expense Details</h1>
    <div class="bg-white p-6 rounded shadow">
        <p><strong>Category:</strong> {{ $expense->category }}</p>
        <p><strong>Description:</strong> {{ $expense->description }}</p>
        <p><strong>Amount:</strong> ${{ number_format($expense->amount, 2) }}</p>
        <p><strong>Date:</strong> {{ $expense->date->format('Y-m-d') }}</p>
        <a href="{{ route('expenses.create', $expense) }}" class="bg-green-500 text-white px-4 py-2 rounded mt-4 inline-block">Edit</a>
    </div>
@endsection