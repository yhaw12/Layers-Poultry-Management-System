@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Add Vaccination Log</h1>

    <!-- Error/Success Messages -->
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Form -->
    <form method="POST" action="{{ route('vaccination-logs.store') }}" class="max-w-lg">
        @csrf
        <div class="mb-4">
            <label for="bird_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bird</label>
            <select name="bird_id" id="bird_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                <option value="">Select a bird</option>
                @foreach ($birds as $bird)
                    <option value="{{ $bird->id }}">{{ $bird->name }}</option>
                @endforeach
            </select>
            @error('bird_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="vaccine_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vaccine Name</label>
            <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
            @error('vaccine_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="date_administered" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Administered</label>
            <input type="date" name="date_administered" id="date_administered" value="{{ old('date_administered') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
            @error('date_administered')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
            <textarea name="notes" id="notes" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="next_vaccination_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Next Vaccination Date</label>
            <input type="date" name="next_vaccination_date" id="next_vaccination_date" value="{{ old('next_vaccination_date') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            @error('next_vaccination_date')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-500 focus:ring-opacity-50">Save</button>
            <a href="{{ route('vaccination-logs.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
