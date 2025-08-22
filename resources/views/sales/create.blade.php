
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Sale</h2>
        </section>

        <!-- Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
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

                    <!-- Customer Name -->
                    <div>
                        <label for="customer_name" class="block text-gray-700 dark:text-gray-300">Customer Name <span class="text-red-600">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" list="customer_suggestions"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_name') border-red-500 @enderror"
                               required aria-describedby="customer_name-error">
                        <datalist id="customer_suggestions">
                            @foreach($customers as $customer)
                                <option value="{{ $customer->name }}">
                            @endforeach
                        </datalist>
                        @error('customer_name')
                            <p id="customer_name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Customer Phone -->
                    <div>
                        <label for="customer_phone" class="block text-gray-700 dark:text-gray-300">Customer Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_phone') border-red-500 @enderror"
                               aria-describedby="customer_phone-error">
                        @error('customer_phone')
                            <p id="customer_phone-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Product Type -->
                    <div>
                        <label for="saleable_type" class="block text-gray-700 dark:text-gray-300">Product Type <span class="text-red-600">*</span></label>
                        <select name="saleable_type" id="saleable_type" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('saleable_type') border-red-500 @enderror"
                                required aria-describedby="saleable_type-error">
                            <option value="" {{ old('saleable_type') ? '' : 'selected' }} disabled>Select Type</option>
                            <option value="App\Models\Bird" {{ old('saleable_type') == 'App\Models\Bird' ? 'selected' : '' }}>Bird</option>
                            <option value="App\Models\Egg" {{ old('saleable_type') == 'App\Models\Egg' ? 'selected' : '' }}>Egg</option>
                        </select>
                        @error('saleable_type')
                            <p id="saleable_type-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Product -->
                    <div>
                        <label for="saleable_id" class="block text-gray-700 dark:text-gray-300">Product <span class="text-red-600">*</span></label>
                        <select name="saleable_id" id="saleable_id" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('saleable_id') border-red-500 @enderror"
                                required aria-describedby="saleable_id-error">
                            <option value="" {{ old('saleable_id') ? '' : 'selected' }} disabled>Select Product</option>
                        </select>
                        @error('saleable_id')
                            <p id="saleable_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Product Variant -->
                    <div>
                        <label for="product_variant" class="block text-gray-700 dark:text-gray-300">Product Variant <span class="text-red-600">*</span></label>
                        <select name="product_variant" id="product_variant" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('product_variant') border-red-500 @enderror"
                                required aria-describedby="product_variant-error">
                            <option value="" {{ old('product_variant') ? '' : 'selected' }} disabled>Select Variant</option>
                        </select>
                        @error('product_variant')
                            <p id="product_variant-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity (Crates for Eggs, Number for Birds) <span class="text-red-600">*</span></label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror"
                               min="1" required aria-describedby="quantity-error">
                        @error('quantity')
                            <p id="quantity-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Unit Price -->
                    <div>
                        <label for="unit_price" class="block text-gray-700 dark:text-gray-300">Unit Price <span class="text-red-600">*</span></label>
                        <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" step="0.01"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('unit_price') border-red-500 @enderror"
                               min="0" required aria-describedby="unit_price-error">
                        @error('unit_price')
                            <p id="unit_price-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Sale Date -->
                    <div>
                        <label for="sale_date" class="block text-gray-700 dark:text-gray-300">Sale Date <span class="text-red-600">*</span></label>
                        <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', now()->format('Y-m-d')) }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sale_date') border-red-500 @enderror"
                               required aria-describedby="sale_date-error">
                        @error('sale_date')
                            <p id="sale_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-gray-700 dark:text-gray-300">Due Date</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('due_date') border-red-500 @enderror"
                               aria-describedby="due_date-error">
                        @error('due_date')
                            <p id="due_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Save
                        </button>
                        <a href="{{ route('sales.index') }}"
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
            const saleableTypeSelect = document.getElementById('saleable_type');
            const saleableIdSelect = document.getElementById('saleable_id');
            const productVariantSelect = document.getElementById('product_variant');

            function populateSaleableOptions(type) {
                saleableIdSelect.innerHTML = '<option value="" disabled selected>Select Product</option>';
                productVariantSelect.innerHTML = '<option value="" disabled selected>Select Variant</option>';

                try {
                    if (type === 'App\\Models\\Bird') {
                        const birds = @json($birds ?? []);
                        if (birds.length === 0) {
                            saleableIdSelect.innerHTML = '<option value="" disabled>No birds available</option>';
                            return;
                        }
                        birds.forEach(bird => {
                            const option = document.createElement('option');
                            option.value = bird.id;
                            option.text = `${bird.breed} (${bird.type})`;
                            option.selected = '{{ old('saleable_id') }}' == bird.id;
                            saleableIdSelect.appendChild(option);
                        });
                        ['broiler', 'layer'].forEach(variant => {
                            const option = document.createElement('option');
                            option.value = variant;
                            option.text = variant.charAt(0).toUpperCase() + variant.slice(1);
                            option.selected = '{{ old('product_variant') }}' === variant;
                            productVariantSelect.appendChild(option);
                        });
                    } else if (type === 'App\\Models\\Egg') {
                        const eggs = @json($eggs ?? []);
                        if (eggs.length === 0) {
                            saleableIdSelect.innerHTML = '<option value="" disabled>No eggs available</option>';
                            return;
                        }
                        eggs.forEach(egg => {
                            const option = document.createElement('option');
                            option.value = egg.id;
                            option.text = `Egg Batch ${egg.id} (${egg.date_laid})`;
                            option.selected = '{{ old('saleable_id') }}' == egg.id;
                            saleableIdSelect.appendChild(option);
                        });
                        ['big', 'small', 'cracked'].forEach(variant => {
                            const option = document.createElement('option');
                            option.value = variant;
                            option.text = variant.charAt(0).toUpperCase() + variant.slice(1);
                            option.selected = '{{ old('product_variant') }}' === variant;
                            productVariantSelect.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error populating saleable options:', error);
                    saleableIdSelect.innerHTML = '<option value="" disabled>Error loading products</option>';
                }
            }

            // Initialize on page load to handle old input
            if (saleableTypeSelect.value) {
                populateSaleableOptions(saleableTypeSelect.value);
            }

            // Update on change
            saleableTypeSelect.addEventListener('change', function() {
                populateSaleableOptions(this.value);
            });
        });
    </script>
@endpush
@endsection
