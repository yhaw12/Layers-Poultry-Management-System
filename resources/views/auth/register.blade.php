@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Register</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
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
            <div>
                <label for="password-confirm" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required 
                       class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-200 font-semibold">
                Register
            </button>
        </form>
        <p class="mt-4 text-center text-gray-600">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline font-medium">Login here</a>
        </p>
    </div>
</div>
@endsection