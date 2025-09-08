@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-12 space-y-16 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Health Check</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 mt-2">Update the health check details for the bird batch below.</p>
        </section>

        <!-- Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-8 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 max-w-lg mx-auto">
                <form method="POST" action="{{ route('health-checks.update', $healthCheck->id) }}" class="space-y-8">
                    @csrf
                    @method('PUT')
                    <!-- Success/Error Messages -->
                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Bird -->
                    <div>
                        <label for="bird_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bird <span class="text-red-600">*</span></label>
                        <select name="bird_id" id="bird_id" class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('bird_id') border-red-500 @enderror" required aria-describedby="bird_id-error">
                            <option value="" {{ old('bird_id', $healthCheck->bird_id) ? '' : 'selected' }} disabled>Select Bird</option>
                            @foreach ($birds as $bird)
                                <option value="{{ $bird->id }}" {{ old('bird_id', $healthCheck->bird_id) == $bird->id ? 'selected' : '' }}>
                                    {{ $bird->breed }} ({{ ucfirst($bird->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('bird_id')
                            <p id="bird_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date <span class="text-red-600">*</span></label>
                        <input name="date" type="date" id="date" value="{{ old('date', $healthCheck->date ? $healthCheck->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                               class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('date') border-red-500 @enderror"
                               required aria-describedby="date-error">
                        @error('date')
                            <p id="date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-600">*</span></label>
                        <input name="status" type="text" id="status" value="{{ old('status', $healthCheck->status) }}"
                               class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('status') border-red-500 @enderror"
                               required aria-describedby="status-error">
                        @error('status')
                            <p id="status-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Symptoms -->
                    <div>
                        <label for="symptoms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Symptoms</label>
                        <textarea name="symptoms" id="symptoms" class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('symptoms') border-red-500 @enderror"
                                  aria-describedby="symptoms-error">{{ old('symptoms', $healthCheck->symptoms) }}</textarea>
                        @error('symptoms')
                            <p id="symptoms-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Treatment -->
                    <div>
                        <label for="treatment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Treatment</label>
                        <textarea name="treatment" id="treatment" class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('treatment') border-red-500 @enderror"
                                  aria-describedby="treatment-error">{{ old('treatment', $healthCheck->treatment) }}</textarea>
                        @error('treatment')
                            <p id="treatment-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('notes') border-red-500 @enderror"
                                  aria-describedby="notes-error">{{ old('notes', $healthCheck->notes) }}</textarea>
                        @error('notes')
                            <p id="notes-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('health-checks.index') }}"
                           class="inline-flex items-center bg-gray-300 text-gray-800 py-2 px-6 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors duration-200 font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors duration-200 font-medium">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection