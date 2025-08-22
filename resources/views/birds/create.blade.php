@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Bird Batch</h2>
        </section>

        <!-- Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                <form method="POST" action="{{ route('birds.store') }}" class="space-y-6">
                    @csrf
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

                    <!-- Breed -->
                    <div>
                        <label for="breed" class="block text-gray-700 dark:text-gray-300">Breed <span class="text-red-600">*</span></label>
                        <input name="breed" type="text" id="breed" value="{{ old('breed') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('breed') border-red-500 @enderror"
                               required aria-describedby="breed-error">
                        @error('breed')
                            <p id="breed-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-gray-700 dark:text-gray-300">Type <span class="text-red-600">*</span></label>
                        <select name="type" id="type" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('type') border-red-500 @enderror" required>
                            <option value="" {{ old('type') ? '' : 'selected' }} disabled>Select Type</option>
                            <option value="layer" {{ old('type') == 'layer' ? 'selected' : '' }}>Layer</option>
                            <option value="broiler" {{ old('type') == 'broiler' ? 'selected' : '' }}>Broiler</option>
                        </select>
                        @error('type')
                            <p id="type-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Stage -->
                    <div>
                        <label for="stage" class="block text-gray-700 dark:text-gray-300">Stage <span class="text-red-600">*</span></label>
                        <select name="stage" id="stage" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('stage') border-red-500 @enderror" required>
                            <option value="" {{ old('stage') ? '' : 'selected' }} disabled>Select Stage</option>
                            <option value="chick" {{ old('stage') == 'chick' ? 'selected' : '' }}>Chick</option>
                            <option value="juvenile" {{ old('stage') == 'juvenile' ? 'selected' : '' }}>Juvenile (Growing Bird)</option>
                            <option value="adult" {{ old('stage') == 'adult' ? 'selected' : '' }}>Adult (Fully Grown)</option>
                        </select>
                        @error('stage')
                            <p id="stage-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity <span class="text-red-600">*</span></label>
                        <input name="quantity" type="number" id="quantity" value="{{ old('quantity') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror"
                               min="1" required aria-describedby="quantity-error">
                        @error('quantity')
                            <p id="quantity-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Chick Fields (Conditional) -->
                    <div class="chick-fields hidden space-y-6">
                        <div>
                            <label for="quantity_bought" class="block text-gray-700 dark:text-gray-300">Quantity Bought</label>
                            <input name="quantity_bought" type="number" id="quantity_bought" value="{{ old('quantity_bought') }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity_bought') border-red-500 @enderror"
                                   min="1" aria-describedby="quantity_bought-error">
                            @error('quantity_bought')
                                <p id="quantity_bought-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div>
                            <label for="feed_amount" class="block text-gray-700 dark:text-gray-300">Feed Amount (kg)</label>
                            <input name="feed_amount" type="number" step="0.01" id="feed_amount" value="{{ old('feed_amount') }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('feed_amount') border-red-500 @enderror"
                                   min="0" aria-describedby="feed_amount-error">
                            @error('feed_amount')
                                <p id="feed_amount-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div>
                            <label for="alive" class="block text-gray-700 dark:text-gray-300">Alive</label>
                            <input name="alive" type="number" id="alive" value="{{ old('alive') }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('alive') border-red-500 @enderror"
                                   min="0" aria-describedby="alive-error">
                            @error('alive')
                                <p id="alive-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div>
                            <label for="dead" class="block text-gray-700 dark:text-gray-300">Dead</label>
                            <input name="dead" type="number" id="dead" value="{{ old('dead') }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('dead') border-red-500 @enderror"
                                   min="0" aria-describedby="dead-error">
                            @error('dead')
                                <p id="dead-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-gray-700 dark:text-gray-300">Purchase Date</label>
                            <input name="purchase_date" type="date" id="purchase_date" value="{{ old('purchase_date') }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('purchase_date') border-red-500 @enderror"
                                   aria-describedby="purchase_date-error">
                            @error('purchase_date')
                                <p id="purchase_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                        <div>
                            <label for="cost" class="block text-gray-700 dark:text-gray-300">Cost ($)</label>
                            <input name="cost" type="number" step="0.01" id="cost" value="{{ old('cost') }}"
                                   class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('cost') border-red-500 @enderror"
                                   min="0" aria-describedby="cost-error">
                            @error('cost')
                                <p id="cost-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @endError
                        </div>
                    </div>

                    <!-- Working -->
                    <div>
                        <label for="working" class="block text-gray-700 dark:text-gray-300">Working <span class="text-red-600">*</span></label>
                        <select name="working" id="working" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('working') border-red-500 @enderror" required>
                            <option value="1" {{ old('working') == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('working') == 0 ? 'selected' : '' }}>No</option>
                        </select>
                        @error('working')
                            <p id="working-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Entry Date -->
                    <div>
                        <label for="entry_date" class="block text-gray-700 dark:text-gray-300">Entry Date <span class="text-red-600">*</span></label>
                        <input name="entry_date" type="date" id="entry_date" value="{{ old('entry_date') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('entry_date') border-red-500 @enderror"
                               required aria-describedby="entry_date-error">
                        @error('entry_date')
                            <p id="entry_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Vaccination Status -->
                    <div>
                        <label for="vaccination_status" class="block text-gray-700 dark:text-gray-300">Vaccination Status</label>
                        <select name="vaccination_status" id="vaccination_status" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('vaccination_status') border-red-500 @enderror">
                            <option value="" {{ old('vaccination_status') ? '' : 'selected' }} disabled>Select Status</option>
                            <option value="1" {{ old('vaccination_status') == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('vaccination_status') == 0 ? 'selected' : '' }}>No</option>
                        </select>
                        @error('vaccination_status')
                            <p id="vaccination_status-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Housing Location -->
                    <div>
                        <label for="housing_location" class="block text-gray-700 dark:text-gray-300">Housing Location</label>
                        <input name="housing_location" type="text" id="housing_location" value="{{ old('housing_location') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('housing_location') border-red-500 @enderror"
                               aria-describedby="housing_location-error">
                        @error('housing_location')
                            <p id="housing_location-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Save
                        </button>
                        <a href="{{ route('birds.index') }}"
                           class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stageSelect = document.getElementById('stage');
            const chickFields = document.querySelector('.chick-fields');

            function toggleChickFields() {
                if (stageSelect.value === 'chick') {
                    chickFields.classList.remove('hidden');
                    chickFields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
                } else {
                    chickFields.classList.add('hidden');
                    chickFields.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
                }
            }

            // Run on page load to handle old input
            toggleChickFields();
            // Run on stage change
            stageSelect.addEventListener('change', toggleChickFields);
        });
    </script>
@endpush
@endsection
