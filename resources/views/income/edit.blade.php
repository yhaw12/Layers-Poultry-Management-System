@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Income</h1>
    <form action="{{ route('income.index', $income) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700">Category</label>
            <select name="category" class="w-full p-2 border rounded">
                <option value="Structure" {{ $income->category == 'Structure' ? 'selected' : '' }}>Structure</option>
                <option value="Feed" {{ $income->category == 'Feed' ? 'selected' : '' }}>Feed</option>
                <!-- Add other options -->
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Description</label>
            <input type="text" name="description" value="{{ $incoome->description }}" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Amount</label>
            <input type="number" name="amount" value="{{ $income->amount }}" step="0.01" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Date</label>
            <input type="date" name="date" value="{{ $income->date->format('Y-m-d') }}" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </form>
@endsection