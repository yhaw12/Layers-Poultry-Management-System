@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md">
    <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-white">Register</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('name') border-red-500 @enderror"
                       aria-describedby="name-error">
                @error('name')
                    <span id="name-error" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('email') border-red-500 @enderror"
                       aria-describedby="email-error">
                @error('email')
                    <span id="email-error" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Password</label>
                <input id="password" type="password" name="password" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('password') border-red-500 @enderror"
                       aria-describedby="password-error">
                @error('password')
                    <span id="password-error" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('password_confirmation') border-red-500 @enderror"
                       aria-describedby="password-confirmation-error">
                @error('password_confirmation')
                    <span id="password-confirmation-error" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold dark:bg-blue-500 dark:hover:bg-blue-600">
                Register
            </button>
        </form>
        <p class="mt-4 text-center text-gray-600 dark:text-gray-300">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline font-medium dark:text-blue-400">Login here</a>
        </p>
    </div>
</div>
@endsection
