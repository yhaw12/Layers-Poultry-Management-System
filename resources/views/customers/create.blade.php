@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Customer</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-gray-700 dark:text-gray-300">Name</label>
                    <input name="name" type="text" id="name" value="{{ old('name') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="phone" class="block text-gray-700 dark:text-gray-300">Phone</label>
                    <input name="phone" type="text" id="phone" value="{{ old('phone') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('phone') border-red-500 @enderror" required>
                    @error('phone')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Save
                    </button>
                    <a href="{{ route('customers.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection