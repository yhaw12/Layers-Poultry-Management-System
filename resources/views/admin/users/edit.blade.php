@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">{{ auth()->user()->id === $user->id ? 'Edit Profile' : 'Edit User' }}</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ auth()->user()->id === $user->id ? route('profile.update') : route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-gray-700 dark:text-gray-300">Name</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 dark:text-gray-300">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 dark:text-gray-300">Password (leave blank to keep current)</label>
            <input type="password" name="password" id="password" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
        </div>
        @if(auth()->user()->hasRole('admin') && auth()->user()->id !== $user->id)
            <div class="mb-4">
                <label for="role" class="block text-gray-700 dark:text-gray-300">Role</label>
                <select name="role" id="role" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">{{ auth()->user()->id === $user->id ? 'Update Profile' : 'Update User' }}</button>
    </form>

    @if(auth()->user()->hasRole('admin') && auth()->user()->id !== $user->id)
        <h2 class="text-xl font-bold mt-6 mb-4">Permissions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach (\Spatie\Permission\Models\Permission::all() as $permission)
                <div class="flex items-center">
                    <form action="{{ route('users.toggle-permission', $user) }}" method="POST" class="flex items-center">
                        @csrf
                        <input type="hidden" name="permission" value="{{ $permission->name }}">
                        <label class="switch mr-2">
                            <input type="checkbox" {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="slider round"></span>
                        </label>
                        <span>{{ $permission->name }}</span>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
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
