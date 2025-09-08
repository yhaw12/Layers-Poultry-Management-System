@extends('layouts.app')

@section('title', 'Edit Disease: {{ $disease->name }}')

@section('content')
<div class="container mx-auto px-6 py-12 space-y-16 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
    <!-- Header -->
    <section>
        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">Edit Disease: {{ $disease->name }}</h2>
        <p class="text-base text-gray-600 dark:text-gray-400 mt-2">Update the details for the disease below.</p>
    </section>

    <!-- Form -->
    <section>
        <div class="bg-gradient-to-r from-white to-gray-100 dark:from-[#1a1a3a] dark:to-gray-800 p-8 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-lg mx-auto">
            <form method="POST" action="{{ route('diseases.update', $disease->id) }}" class="space-y-8" id="edit-disease-form">
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

                <!-- Disease Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Disease Name <span class="text-red-600">*</span></label>
                    <input name="name" type="text" id="name" value="{{ old('name', $disease->name) }}"
                           class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                           required placeholder="Enter disease name" aria-describedby="name-error">
                    @error('name')
                        <p id="name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date <span class="text-red-600">*</span></label>
                    <input name="start_date" type="date" id="start_date" value="{{ old('start_date', $disease->start_date ? now()->format('Y-m-d') : '') }}"
                           class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('start_date') border-red-500 @enderror"
                           required aria-describedby="start_date-error">
                    @error('start_date')
                        <p id="start_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description"
                              class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('description') border-red-500 @enderror"
                              placeholder="Enter disease description" aria-describedby="description-error">{{ old('description', $disease->description) }}</textarea>
                    @error('description')
                        <p id="description-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>

                <!-- Symptoms -->
                <div>
                    <label for="symptoms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Symptoms</label>
                    <textarea name="symptoms" id="symptoms"
                              class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('symptoms') border-red-500 @enderror"
                              placeholder="Enter disease symptoms" aria-describedby="symptoms-error">{{ old('symptoms', $disease->symptoms) }}</textarea>
                    @error('symptoms')
                        <p id="symptoms-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>

                <!-- Treatments -->
                <div>
                    <label for="treatments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Treatments</label>
                    <textarea name="treatments" id="treatments"
                              class="mt-1 w-full p-3 border rounded-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 transition duration-200 @error('treatments') border-red-500 @enderror"
                              placeholder="Enter disease treatments" aria-describedby="treatments-error">{{ old('treatments', $disease->treatments) }}</textarea>
                    @error('treatments')
                        <p id="treatments-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @endError
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('diseases.index') }}"
                       class="inline-flex items-center bg-gray-300 text-gray-800 py-2 px-6 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 transition-colors duration-200 font-medium"
                       aria-label="Cancel">
                        Cancel
                    </a>
                    <button type="submit" id="edit-disease-btn"
                            class="inline-flex items-center bg-blue-600 text-white py-2 px-6 rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 transition font-medium"
                            aria-label="Update disease">
                        <span class="flex items-center">
                            <svg id="edit-spinner" class="hidden w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 0116 0"></path>
                            </svg>
                            <span class="mr-2" aria-hidden="true">✏️</span> Update Disease
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
(function () {
    const editForm = document.getElementById('edit-disease-form');
    const editButton = document.getElementById('edit-disease-btn');
    const editSpinner = document.getElementById('edit-spinner');

    // Toast helper
    function toast(message, type = 'info', timeout = 3000) {
        const id = 't-' + Date.now();
        const colors = {
            info: 'bg-indigo-600 text-white',
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white'
        };
        const el = document.createElement('div');
        el.id = id;
        el.className = `mb-3 px-4 py-2 rounded shadow ${colors[type] || colors.info} max-w-sm flex justify-between items-center fixed top-4 right-4 z-50`;
        el.innerHTML = `
            <span>${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" aria-label="Dismiss toast">✕</button>
        `;
        const toastContainer = document.getElementById('toast-container') || document.body;
        toastContainer.appendChild(el);
        const closeBtn = el.querySelector('button');
        closeBtn.addEventListener('click', () => el.remove());
        setTimeout(() => {
            el.classList.add('opacity-0', 'transition', 'duration-300');
            setTimeout(() => el.remove(), 350);
        }, timeout);
    }

    // Show server-set messages
    @if (session('success'))
        toast('{{ session('success') }}', 'success', 4000);
    @endif
    @if (session('error'))
        toast('{{ session('error') }}', 'error', 4000);
    @endif

    // Client-side validation
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.currentTarget;
        const formData = new FormData(form);
        const name = formData.get('name');
        const startDate = formData.get('start_date');

        // Client-side validation
        if (!name || name.trim() === '') {
            toast('The name field is required.', 'error', 4000);
            return;
        }
        if (!startDate || !/^\d{4}-\d{2}-\d{2}$/.test(startDate)) {
            toast('The start date must be a valid date.', 'error', 4000);
            return;
        }

        editButton.disabled = true;
        editSpinner.classList.remove('hidden');

        try {
            const formData = new FormData(form);
        formData.append('_method', 'PUT'); // <-- force PUT so Laravel sees it

        const response = await fetch(form.action, {
            method: 'POST', // Laravel will treat this as PUT because of _method
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

            const data = await response.json();

            if (response.ok && data.success) {
                toast(data.message || 'Disease updated successfully.', 'success', 2000);
                window.location.href = '{{ route('diseases.index') }}';
            } else {
                if (response.status === 422 && data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    toast(errorMessages || 'Failed to update disease.', 'error', 4000);
                } else {
                    toast(data.message || 'Failed to update disease.', 'error', 3000);
                }
            }
        } catch (err) {
            console.error('Form submission error:', err);
            toast('Network error. Please try again.', 'error', 3000);
        } finally {
            editButton.disabled = false;
            editSpinner.classList.add('hidden');
        }
    });
})();
</script>
@endpush
@endsection