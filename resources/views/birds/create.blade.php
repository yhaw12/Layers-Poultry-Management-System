@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Bird Batch</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            <form method="POST" action="{{ route('birds.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="breed" class="block text-gray-700 dark:text-gray-300">Breed</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('breed')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="type" class="block text-gray-700 dark:text-gray-300">Type</label>
                    <select name="type" id="type" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="" {{ old('type') ? '' : 'selected' }} disabled>Select Type</option>
                        <option value="layer" {{ old('type') == 'layer' ? 'selected' : '' }}>Layer</option>
                        <option value="broiler" {{ old('type') == 'broiler' ? 'selected' : '' }}>Broiler</option>
                    </select>
                    @error('type')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="1" required>
                    @error('quantity')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="working" class="block text-gray-700 dark:text-gray-300">Working</label>
                    <select name="working" id="working" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="1" {{ old('working') == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('working') == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    @error('working')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="age" class="block text-gray-700 dark:text-gray-300">Age (Weeks)</label>
                    <input type="number" name="age" id="age" value="{{ old('age') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                    @error('age')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="mb-4">
    <label class="block text-gray-700 dark:text-gray-300">Vaccination Status</label>
    <input type="text" name="vaccination_status" class="w-full border rounded p-2 dark:bg-gray-800">
</div>
<div class="mb-4">
    <label class="block text-gray-700 dark:text-gray-300">Housing Location</label>
    <input type="text" name="housing_location" class="w-full border rounded p-2 dark:bg-gray-800">
</div>
<div class="mb-4">
    <label class="block text-gray-700 dark:text-gray-300">Stage</label>
    <select name="stage" class="w-full border rounded p-2 dark:bg-gray-800">
        <option value="chick">Chick</option>
        <option value="grower">Grower</option>
        <option value="layer">Layer</option>
    </select>
</div>
                <div>
                    <label for="entry_date" class="block text-gray-700 dark:text-gray-300">Entry Date</label>
                    <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('entry_date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Save
                    </button>
                    <a href="{{ route('birds.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection