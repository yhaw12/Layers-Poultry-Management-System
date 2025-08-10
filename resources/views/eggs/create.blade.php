@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-6">Add Egg Record</h2>
    <form method="POST" action="{{ route('eggs.store') }}"
        class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Pen / Flock</label>
                <select name="pen_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">Select Pen (Optional)</option>
                    @foreach ($pens as $pen)
                        <option value="{{ $pen->id }}" {{ old('pen_id') == $pen->id ? 'selected' : '' }}>{{ $pen->name }}</option>
                    @endforeach
                </select>
                @error('pen_id')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Number of Crates</label>
                <input type="number" name="crates" value="{{ old('crates') }}" step="0.01" min="0"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                @error('crates')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">Additional Eggs (0-29)</label>
                <input type="number" name="additional_eggs" value="{{ old('additional_eggs') }}" min="0" max="29"
                    class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                @error('additional_eggs')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_cracked" value="1" {{ old('is_cracked') ? 'checked' : '' }}
                        class="mr-2" id="is_cracked">
                    Cracked Eggs
                </label>
                @error('is_cracked')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div id="egg_size_div" class="{{ old('is_cracked') ? 'hidden' : '' }}">
                <label class="block text-gray-700 dark:text-gray-300">Egg Size</label>
                <select name="egg_size" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    <option value="">Select Size</option>
                    <option value="small" {{ old('egg_size') == 'small' ? 'selected' : '' }}>Small</option>
                    <option value="medium" {{ old('egg_size') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="large" {{ old('egg_size') == 'large' ? 'selected' : '' }}>Large</option>
                </select>
                @error('egg_size')
                    <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-6">
            <button type="submit"
                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Save
            </button>
            <a href="{{ route('eggs.index') }}"
                class="ml-4 text-gray-600 dark:text-gray-300 hover:underline">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('is_cracked').addEventListener('change', function () {
        document.getElementById('egg_size_div').classList.toggle('hidden', this.checked);
    });
</script>
@endsection