{{-- sales.create.blade.php (update form section) --}}
@extends('layouts.app')

@section('content')
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
            @csrf
            <!-- Existing fields: customer_name, customer_phone, saleable_type, saleable_id, product_variant, quantity, unit_price, sale_date -->
            <div>
                <label for="customer_name" class="block text-gray-700 dark:text-gray-300">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" list="customer_suggestions" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                <datalist id="customer_suggestions">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->name }}">
                    @endforeach
                </datalist>
                @error('customer_name')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="customer_phone" class="block text-gray-700 dark:text-gray-300">Customer Phone (Optional)</label>
                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('customer_phone')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="saleable_type" class="block text-gray-700 dark:text-gray-300">Product Type</label>
                <select name="saleable_type" id="saleable_type" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    <option value="" disabled selected>Select Type</option>
                    <option value="App\Models\Bird" {{ old('saleable_type') == 'App\Models\Bird' ? 'selected' : '' }}>Bird</option>
                    <option value="App\Models\Egg" {{ old('saleable_type') == 'App\Models\Egg' ? 'selected' : '' }}>Egg</option>
                </select>
                @error('saleable_type')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="saleable_id" class="block text-gray-700 dark:text-gray-300">Product</label>
                <select name="saleable_id" id="saleable_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    <option value="" disabled selected>Select Product</option>
                </select>
                @error('saleable_id')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="product_variant" class="block text-gray-700 dark:text-gray-300">Product Variant</label>
                <select name="product_variant" id="product_variant" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    <option value="" disabled selected>Select Variant</option>
                </select>
                @error('product_variant')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity (Crates for Eggs, Number for Birds)</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="1" required>
                @error('quantity')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="unit_price" class="block text-gray-700 dark:text-gray-300">Unit Price</label>
                <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" step="0.01" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                @error('unit_price')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="sale_date" class="block text-gray-700 dark:text-gray-300">Sale Date</label>
                <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', now()->format('Y-m-d')) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                @error('sale_date')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div>
                <label for="due_date" class="block text-gray-700 dark:text-gray-300">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                @error('due_date')
                    <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @endError
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Save
                </button>
                <a href="{{ route('sales.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saleableTypeSelect = document.getElementById('saleable_type');
    const saleableIdSelect = document.getElementById('saleable_id');
    const productVariantSelect = document.getElementById('product_variant');
    const customerInput = document.getElementById('customer_name');
    const customers = @json($customers ?? []);

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
                    saleableIdSelect.appendChild(option);
                });
                ['broiler', 'layer'].forEach(variant => {
                    const option = document.createElement('option');
                    option.value = variant;
                    option.text = variant.charAt(0).toUpperCase() + variant.slice(1);
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
                    saleableIdSelect.appendChild(option);
                });
                ['big', 'small', 'cracked'].forEach(variant => {
                    const option = document.createElement('option');
                    option.value = variant;
                    option.text = variant.charAt(0).toUpperCase() + variant.slice(1);
                    productVariantSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error populating saleable options:', error);
            saleableIdSelect.innerHTML = '<option value="" disabled>Error loading products</option>';
        }
    }

    saleableTypeSelect.addEventListener('change', function() {
        populateSaleableOptions(this.value);
    });
});
</script>
@endsection