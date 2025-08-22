@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <!-- Header -->
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Supplier</h2>
        </section>

        <!-- Form -->
        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
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

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-gray-700 dark:text-gray-300">Supplier Name <span class="text-red-600">*</span></label>
                        <input name="name" type="text" id="name" value="{{ old('name') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror"
                               required aria-describedby="name-error">
                        @error('name')
                            <p id="name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Contact -->
                    <div>
                        <label for="contact" class="block text-gray-700 dark:text-gray-300">Contact Number</label>
                        <input name="contact" type="text" id="contact" value="{{ old('contact') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('contact') border-red-500 @enderror"
                               aria-describedby="contact-error">
                        @error('contact')
                            <p id="contact-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-gray-700 dark:text-gray-300">Email Address</label>
                        <input name="email" type="email" id="email" value="{{ old('email') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('email') border-red-500 @enderror"
                               aria-describedby="email-error">
                        @error('email')
                            <p id="email-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @endError
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4">
                        <button type="submit"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Save
                        </button>
                        <a href="{{ route('suppliers.index') }}"
                           class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
