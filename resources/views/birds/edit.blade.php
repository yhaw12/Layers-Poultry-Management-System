@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Bird Batch</h2>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md max-w-md mx-auto">
            <form method="POST" action="{{ route('birds.update', $bird->id) }}" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label for="breed" class="block text-gray-700 dark:text-gray-300">Breed</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed', $bird->breed) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('breed')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="type" class="block text-gray-700 dark:text-gray-300">Type</label>
                    <select name="type" id="type" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="" disabled>Select Type</option>
                        <option value="layer" {{ old('type', $bird->type) == 'layer' ? 'selected' : '' }}>Layer</option>
                        <option value="broiler" {{ old('type', $bird->type) == 'broiler' ? 'selected' : '' }}>Broiler</option>
                    </select>
                    @error('type')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="stage" class="block text-gray-700 dark:text-gray-300">Stage</label>
                    <select name="stage" id="stage" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="" disabled>Select Stage</option>
                        <option value="chick" {{ old('stage', $bird->stage) == 'chick' ? 'selected' : '' }}>Chick</option>
                        <option value="juvenile" {{ old('stage', $bird->stage) == 'juvenile' ? 'selected' : '' }}>Juvenile (Growing Bird)</option>
                        <option value="adult" {{ old('stage', $bird->stage) == 'adult' ? 'selected' : '' }}>Adult (Fully Grown)</option>
                    </select>
                    @error('stage')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $bird->quantity) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="1" required>
                    @error('quantity')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="chick-fields {{ $bird->stage == 'chick' ? '' : 'hidden' }} space-y-6">
                    <div>
                        <label for="quantity_bought" class="block text-gray-700 dark:text-gray-300">Quantity Bought</label>
                        <input type="number" name="quantity_bought" id="quantity_bought" value="{{ old('quantity_bought', $bird->quantity_bought) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="1" {{ $bird->stage == 'chick' ? 'required' : '' }}>
                        @error('quantity_bought')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="feed_amount" class="block text-gray-700 dark:text-gray-300">Feed Amount (kg)</label>
                        <input type="number" name="feed_amount" id="feed_amount" value="{{ old('feed_amount', $bird->feed_amount) }}" step="0.01" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" {{ $bird->stage == 'chick' ? 'required' : '' }}>
                        @error('feed_amount')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="alive" class="block text-gray-700 dark:text-gray-300">Alive</label>
                        <input type="number" name="alive" id="alive" value="{{ old('alive', $bird->alive) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" {{ $bird->stage == 'chick' ? 'required' : '' }}>
                        @error('alive')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="dead" class="block text-gray-700 dark:text-gray-300">Dead</label>
                        <input type="number" name="dead" id="dead" value="{{ old('dead', $bird->dead) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" {{ $bird->stage == 'chick' ? 'required' : '' }}>
                        @error('dead')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="purchase_date" class="block text-gray-700 dark:text-gray-300">Purchase Date</label>
                        <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $bird->purchase_date?->format('Y-m-d')) }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" {{ $bird->stage == 'chick' ? 'required' : '' }} max="{{ now()->format('Y-m-d') }}">
                        @error('purchase_date')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="cost" class="block text-gray-700 dark:text-gray-300">Cost ($)</label>
                        <input type="number" name="cost" id="cost" value="{{ old('cost', $bird->cost) }}" step="0.01" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" min="0" {{ $bird->stage == 'chick' ? 'required' : '' }}>
                        @error('cost')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="working" class="block text-gray-700 dark:text-gray-300">Working</label>
                    <select name="working" id="working" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="1" {{ old('working', $bird->working) == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('working', $bird->working) == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    @error('working')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="entry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Entry Date <span class="text-red-600">*</span></label>
                    <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date', $bird->entry_date->format('Y-m-d')) }}" class="mt-1 w-full border rounded-lg p-3 dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200" required max="{{ now()->format('Y-m-d') }}">
                    @error('entry_date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="vaccination_status" class="block text-gray-700 dark:text-gray-300">Vaccination Status</label>
                    <select name="vaccination_status" id="vaccination_status" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="" {{ old('vaccination_status', $bird->vaccination_status) ? '' : 'selected' }} disabled>Select Status</option>
                        <option value="1" {{ old('vaccination_status', $bird->vaccination_status) == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('vaccination_status', $bird->vaccination_status) == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    @error('vaccination_status')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pen_id" class="block text-gray-700 dark:text-gray-300">Pen</label>
                    <select name="pen_id" id="pen_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <option value="" {{ old('pen_id', $bird->pen_id) ? '' : 'selected' }} disabled>Select Pen</option>
                        @foreach($pens as $pen)
                            <option value="{{ $pen->id }}" {{ old('pen_id', $bird->pen_id) == $pen->id ? 'selected' : '' }}>{{ $pen->name }}</option>
                        @endforeach
                    </select>
                    @error('pen_id')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        Update
                    </button>
                    <a href="{{ route('birds.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
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
