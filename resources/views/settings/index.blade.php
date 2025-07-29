@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Settings</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Notification Preferences</h2>
            <div class="flex items-center">
                <label class="switch mr-2">
                    <input type="checkbox" name="notifications[email]" {{ $preferences['notifications']['email'] ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
                <span class="text-gray-700 dark:text-gray-300">Receive email notifications for critical alerts</span>
            </div>
        </div>
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Theme Preference</h2>
            <div class="flex flex-col space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="theme" value="light" class="mr-2" {{ $preferences['theme'] === 'light' ? 'checked' : '' }}>
                    <span class="text-gray-700 dark:text-gray-300">Light</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="theme" value="dark" class="mr-2" {{ $preferences['theme'] === 'dark' ? 'checked' : '' }}>
                    <span class="text-gray-700 dark:text-gray-300">Dark</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="theme" value="system" class="mr-2" {{ $preferences['theme'] === 'system' ? 'checked' : '' }}>
                    <span class="text-gray-700 dark:text-gray-300">System Default</span>
                </label>
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Save Settings</button>
    </form>
</div>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }
    input:checked + .slider {
        background-color: #2196F3;
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    .slider.round {
        border-radius: 34px;
    }
    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endsection
