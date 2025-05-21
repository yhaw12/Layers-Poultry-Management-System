@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Expenses</h1>
    <a href="{{ route('expenses.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Expense</a>
    <table class="w-full bg-white shadow rounded">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Category</th>
                <th class="p-2">Description</th>
                <th class="p-2">Amount</th>
                <th class="p-2">Date</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td class="p-2">{{ $expense->category }}</td>
                <td class="p-2">{{ $expense->description }}</td>
                <td class="p-2">${{ number_format($expense->amount, 2) }}</td>
                <td class="p-2">{{ $expense->date->format('Y-m-d') }}</td>
                <td class="p-2">
                    <a href="{{ route('expenses.show', $expense) }}" class="text-blue-500">View</a>
                    <a href="{{ route('expenses.edit', $expense) }}" class="text-green-500 ml-2">Edit</a>
                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection