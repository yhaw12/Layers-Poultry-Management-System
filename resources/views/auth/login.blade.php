@extends('layouts.app')

@section('content')
<style>
    body {
        background-image: url('{{ asset('images/chicken.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>

<div class="container mx-auto px-4 py-6 max-w-md">
    <div class="bg-white bg-opacity-60 p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Login</h1>
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input id="password" type="password" name="password" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2 text-blue-500 rounded">
                    <span class="text-gray-700">Remember Me</span>
                </label>
                <a href="#" class="text-blue-500 hover:underline text-sm">Forgot Password?</a>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">
                Login
            </button>
        </form>
        <p class="mt-4 text-center text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-blue-500 hover:underline font-medium">Register here</a>
        </p>
    </div>
</div>
@endsection
