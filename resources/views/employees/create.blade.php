@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Employee</h2>
            <a href="{{ route('employees.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                Back to Employees
            </a>
        </div>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-gray-700 dark:text-gray-300">Name</label>
                    <input name="name" type="text" id="name" value="{{ old('name') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="monthly_salary" class="block text-gray-700 dark:text-gray-300">Monthly Salary (KES)</label>
                    <input name="monthly_salary" type="number" step="0.01" id="monthly_salary" value="{{ old('monthly_salary') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('monthly_salary') border-red-500 @enderror" min="0" required>
                    @error('monthly_salary')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="telephone" class="block text-gray-700 dark:text-gray-300">Telephone</label>
                    <input name="telephone" type="text" id="telephone" value="{{ old('telephone') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('telephone') border-red-500 @enderror" maxlength="20">
                    @error('telephone')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600">
                        Save
                    </button>
                    <a href="{{ route('employees.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection