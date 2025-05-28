@extends('layouts.app')

@section('content')
<style>
    body {
        background-image: url('{{ asset('images/chicken.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1); /* Subtle overlay for readability */
        z-index: -1;
    }
    body.dark::before {
        background: rgba(0, 0, 0, 0.4); /* Darker overlay in dark mode */
    }
</style>

<div class="container mx-auto px-4 py-6 max-w-md min-h-screen flex items-center justify-center">
    <div class="bg-white/60 dark:bg-gray-800/60 p-6 rounded-lg shadow-lg backdrop-blur-sm">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-gray-100">Login</h1>
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 dark:border-red-400 @enderror">
                @error('email')
                    <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">Password</label>
                <input id="password" type="password" name="password" required 
                       class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 dark:border-red-400 @enderror">
                @error('password')
                    <span class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2 text-blue-500 dark:text-blue-400 rounded focus:ring-blue-500 dark:focus:ring-blue-400">
                    <span class="text-gray-700 dark:text-gray-300">Remember Me</span>
                </label>
                <a href="" class="text-blue-500 dark:text-blue-400 hover:underline text-sm">Forgot Password?</a>
            </div>
            <button type="submit" class="w-full bg-blue-600 dark:bg-blue-500 text-white py-3 rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition duration-200 font-semibold">
                Login
            </button>
        </form>
        <p class="mt-4 text-center text-gray-600 dark:text-gray-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-500 dark:text-blue-400 hover:underline font-medium">Register here</a>
        </p>
    </div>
</div>
@endsection