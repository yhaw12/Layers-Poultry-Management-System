{{-- @extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <section>
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Log New Health Check</h2>
    </section>
    <section>
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            <form method="POST" action="{{ route('health-checks.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="bird_id" class="block text-gray-700 dark:text-gray-300">Bird</label>
                    <select name="bird_id" id="bird_id" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                        <option value="" disabled selected>Select Bird</option>
                        @foreach ($birds as $bird)
                            <option value="{{ $bird->id }}">{{ $bird->breed }} ({{ ucfirst($bird->type) }})</option>
                        @endforeach
                    </select>
                    @error('bird_id')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="date" class="block text-gray-700 dark:text-gray-300">Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('date')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="status" class="block text-gray-700 dark:text-gray-300">Status</label>
                    <input type="text" name="status" id="status" value="{{ old('status') }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @error('status')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="symptoms" class="block text-gray-700 dark:text-gray-300">Symptoms</label>
                    <textarea name="symptoms" id="symptoms" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('symptoms') }}</textarea>
                    @error('symptoms')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="treatment" class="block text-gray-700 dark:text-gray-300">Treatment</label>
                    <textarea name="treatment" id="treatment" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('treatment') }}</textarea>
                    @error('treatment')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div>
                    <label for="notes" class="block text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea name="notes" id="notes" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"> --}}
                        {{-- Save --}}
                    {{-- </button>
                    <a href="{{ route('health-checks.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection --}}