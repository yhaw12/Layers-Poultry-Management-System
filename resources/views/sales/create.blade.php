@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Sale</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
            <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="customer_id" class="block text-gray-700 dark:text-gray-300">Customer</label>
                    <select name="customer_id" id="customer_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="" disabled selected>Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')
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
                        <!-- Populated dynamically via JavaScript -->
                    </select>
                    @error('saleable_id')
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
                    <label for="unit_price" class="block text-gray-700 dark:text-gray-300">Unit Price</label>
                    <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" step="0.01" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" required>
                    @error('unit_price')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="sale_date" class="block text-gray-700 dark:text-gray-300">Sale Date</label>
                    <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('sale_date')
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
</div>

<script>
document.getElementById('saleable_type').addEventListener('change', function() {
    const type = this.value;
    const saleableIdSelect = document.getElementById('saleable_id');
    saleableIdSelect.innerHTML = '<option value="" disabled selected>Select Product</option>';

    if (type === 'App\\Models\\Bird') {
        const birds = @json($birds);
        birds.forEach(bird => {
            const option = document.createElement('option');
            option.value = bird.id;
            option.text = `${bird.breed} (${bird.type})`;
            saleableIdSelect.appendChild(option);
        });
    } else if (type === 'App\\Models\\Egg') {
        const eggs = @json($eggs);
        eggs.forEach(egg => {
            const option = document.createElement('option');
            option.value = egg.id;
            option.text = `Egg Batch ${egg.id}`;
            saleableIdSelect.appendChild(option);
        });
    }
});
</script>
@endsection