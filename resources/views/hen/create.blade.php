@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Add Expense</h1>
    <form action="{{ route('expenses.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Category</label>
            <select name="category" class="w-full p-2 border rounded">
                <option value="Structure">Structure</option>
                <option value="Feed">Feed</option>
                <option value="Veterinary">Veterinary</option>
                <option value="Utilities">Utilities</option>
                <option value="Labor">Labor</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Description</label>
            <input type="text" name="description" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Amount</label>
            <input type="number" name="amount" step="0.01" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Date</label>
            <input type="date" name="date" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
    </form>
@endsection