@extends('layouts.app')

@section('title', 'Add New Disease')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white max-w-md ">
    <!-- Header -->
    <section class="flex justify-between items-center">
        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Add New Disease</h2>
        {{-- <a href="{{ route('diseases.index') }}" 
           class="inline-flex items-center bg-gray-600 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 transition" 
           aria-label="Back to diseases">
            <span class="mr-2" aria-hidden="true">⬅️</span> Back to Diseases
        </a> --}}
    </section>

    <!-- Create Disease Form -->
    <section>
        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Disease Details</h3>
            @if ($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
            <form action="{{ route('diseases.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Disease Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disease Name <span class="text-red-600">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                           placeholder="Enter disease name" required aria-label="Disease name">
                    @error('name')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date <span class="text-red-600">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}"
                           class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('start_date') border-red-500 @enderror"
                           required aria-label="Start date">
                    @error('start_date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description"
                              class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('description') border-red-500 @enderror"
                              placeholder="Enter disease description" aria-label="Description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <!-- Symptoms -->
                <div>
                    <label for="symptoms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Symptoms</label>
                    <textarea name="symptoms" id="symptoms"
                              class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('symptoms') border-red-500 @enderror"
                              placeholder="Enter disease symptoms" aria-label="Symptoms">{{ old('symptoms') }}</textarea>
                    @error('symptoms')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <!-- Treatments -->
                <div>
                    <label for="treatments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Treatments</label>
                    <textarea name="treatments" id="treatments"
                              class="w-full border rounded-lg p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('treatments') border-red-500 @enderror"
                              placeholder="Enter disease treatments" aria-label="Treatments">{{ old('treatments') }}</textarea>
                    @error('treatments')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <!-- Submit Button -->
                <div class="flex justify-end gap-2">
                    <a href="{{ route('diseases.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition"
                       aria-label="Cancel">Cancel</a>
                    <button type="submit"
                            class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition text-sm"
                            aria-label="Save disease">
                        <span class="flex items-center">
                            <span class="mr-2" aria-hidden="true">💾</span> Save
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection