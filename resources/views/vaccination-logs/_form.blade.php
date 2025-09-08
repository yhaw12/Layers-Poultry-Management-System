<form method="POST" action="{{ $action }}" class="space-y-6" id="{{ $formId ?? 'vaccination-form' }}">
    @csrf
    @if($method ?? false)
        @method($method)
    @endif

    <!-- Bird -->
    <div>
        <label for="bird_id" class="block text-gray-700 dark:text-gray-300">Bird <span class="text-red-600">*</span></label>
        <select name="bird_id" id="bird_id" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('bird_id') border-red-500 @enderror" required aria-describedby="bird_id-error">
            <option value="" {{ old('bird_id') ? '' : 'selected' }} disabled>Select Bird</option>
            @if($birds->isEmpty())
                <option value="" disabled>No birds available. Please add a bird first.</option>
            @else
                @foreach ($birds as $bird)
                    <option value="{{ $bird->id }}" {{ old('bird_id', $log->bird_id ?? '') == $bird->id ? 'selected' : '' }}>{{ $bird->displayName() }}</option>
                @endforeach
            @endif
        </select>
        @error('bird_id')
            <p id="bird_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
        @if($birds->isEmpty())
            <p class="text-yellow-600 dark:text-yellow-400 text-sm mt-1">No birds available. <a href="{{ route('birds.create') }}" class="underline">Add a bird</a>.</p>
        @endif
    </div>

    <!-- Vaccine Name -->
    <div>
        <label for="vaccine_name" class="block text-gray-700 dark:text-gray-300">Vaccine Name <span class="text-red-600">*</span></label>
        <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name', $log->vaccine_name ?? '') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('vaccine_name') border-red-500 @enderror" required aria-describedby="vaccine_name-error">
        @error('vaccine_name')
            <p id="vaccine_name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Date Administered -->
    <div>
        <label for="date_administered" class="block text-gray-700 dark:text-gray-300">Date Administered <span class="text-red-600">*</span></label>
        <input type="date" name="date_administered" id="date_administered" value="{{ old('date_administered', isset($log->date_administered) ? \Carbon\Carbon::parse($log->date_administered)->format('Y-m-d') : '') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('date_administered') border-red-500 @enderror" required aria-describedby="date_administered-error">
        @error('date_administered')
            <p id="date_administered-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Notes -->
    <div>
        <label for="notes" class="block text-gray-700 dark:text-gray-300">Notes</label>
        <textarea name="notes" id="notes" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('notes') border-red-500 @enderror" aria-describedby="notes-error">{{ old('notes', $log->notes ?? '') }}</textarea>
        @error('notes')
            <p id="notes-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Next Vaccination Date -->
    <div>
        <label for="next_vaccination_date" class="block text-gray-700 dark:text-gray-300">Next Vaccination Date</label>
        <input type="date" name="next_vaccination_date" id="next_vaccination_date" value="{{ old('next_vaccination_date', isset($log->next_vaccination_date) ? \Carbon\Carbon::parse($log->next_vaccination_date)->format('Y-m-d') : '') }}" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('next_vaccination_date') border-red-500 @enderror" aria-describedby="next_vaccination_date-error">
        @error('next_vaccination_date')
            <p id="next_vaccination_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Buttons -->
    <div class="flex space-x-4">
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" {{ $birds->isEmpty() ? 'disabled' : '' }}>
            <span class="flex items-center">
                <svg id="form-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path></svg>
                {{ $submitText ?? 'Save' }}
            </span>
        </button>
        <a href="{{ route('vaccination-logs.index') }}" class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
            Cancel
        </a>
    </div>
</form>