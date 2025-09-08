@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Egg Record</h2>
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
            <form method="POST" action="{{ route('eggs.update', $egg) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <!-- Error Messages -->
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

                <!-- Pen / Flock -->
                <div>
                    <label for="pen_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pen / Flock</label>
                    <select name="pen_id" id="pen_id" class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('pen_id') border-red-500 @enderror">
                        <option value="" {{ old('pen_id', $egg->pen_id) ? '' : 'selected' }} disabled>Select Pen</option>
                        @foreach ($pens as $pen)
                            <option value="{{ $pen->id }}" {{ old('pen_id', $egg->pen_id) == $pen->id ? 'selected' : '' }}>{{ $pen->name }}</option>
                        @endforeach
                    </select>
                    @error('pen_id')
                        <p id="pen_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Laid -->
                <div>
                    <label for="date_laid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Laid <span class="text-red-600">*</span></label>
                    <input type="date" name="date_laid" id="date_laid" value="{{ old('date_laid', $egg->date_laid->format('Y-m-d')) }}"
                           class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('date_laid') border-red-500 @enderror"
                           required max="{{ now()->format('Y-m-d') }}" aria-describedby="date_laid-error">
                    @error('date_laid')
                        <p id="date_laid-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Number of Crates -->
                <div>
                    <label for="crates" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of Crates <span class="text-red-600">*</span></label>
                    <input type="number" name="crates" id="crates" value="{{ old('crates', $egg->crates) }}" step="1" min="0"
                           class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('crates') border-red-500 @enderror"
                           required aria-describedby="crates-error">
                    @error('crates')
                        <p id="crates-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Eggs -->
                <div>
                    <label for="additional_eggs" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Eggs (0-29) <span class="text-red-600">*</span></label>
                    <input type="number" name="additional_eggs" id="additional_eggs" value="{{ old('additional_eggs', $egg->additional_eggs) }}" min="0" max="29" step="1"
                           class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('additional_eggs') border-red-500 @enderror"
                           required aria-describedby="additional_eggs-error">
                    @error('additional_eggs')
                        <p id="additional_eggs-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Cracked -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_cracked" id="is_cracked" value="1" {{ old('is_cracked', $egg->is_cracked) ? 'checked' : '' }}
                               class="mr-2 dark:bg-gray-800 dark:border-gray-600">
                        Cracked Eggs
                    </label>
                    @error('is_cracked')
                        <p id="is_cracked-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Egg Size -->
                <div id="egg_size_div" class="{{ old('is_cracked', $egg->is_cracked) ? 'hidden' : '' }}">
                    <label for="egg_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Egg Size</label>
                    <select name="egg_size" id="egg_size" class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('egg_size') border-red-500 @enderror">
                        <option value="">{{ __('Select Size (optional)') }}</option>
                        <option value="small" {{ old('egg_size', $egg->egg_size) == 'small' ? 'selected' : '' }}>Small</option>
                        <option value="medium" {{ old('egg_size', $egg->egg_size) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="large" {{ old('egg_size', $egg->egg_size) == 'large' ? 'selected' : '' }}>Large</option>
                    </select>
                    @error('egg_size')
                        <p id="egg_size-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('eggs.index') }}"
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isCrackedCheckbox = document.getElementById('is_cracked');
            const eggSizeDiv = document.getElementById('egg_size_div');

            function toggleEggSizeField() {
                eggSizeDiv.classList.toggle('hidden', isCrackedCheckbox.checked);
            }

            // Run on page load to handle old input
            toggleEggSizeField();
            // Run on checkbox change
            isCrackedCheckbox.addEventListener('change', toggleEggSizeField);
        });
    </script>
@endpush
@endsection
