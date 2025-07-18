@extends('layouts.app')

     @section('content')
     <div class="container mx-auto px-4 py-8">
         <h1 class="text-2xl font-bold mb-4">Create User</h1>

         <form action="{{ route('users.store') }}" method="POST">
             @csrf
             <div class="mb-4">
                 <label for="name" class="block text-gray-700 dark:text-gray-300">Name</label>
                 <input type="text" name="name" id="name" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
             </div>
             <div class="mb-4">
                 <label for="email" class="block text-gray-700 dark:text-gray-300">Email</label>
                 <input type="email" name="email" id="email" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
             </div>
             <div class="mb-4">
                 <label for="password" class="block text-gray-700 dark:text-gray-300">Password</label>
                 <input type="password" name="password" id="password" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
             </div>
             <div class="mb-4">
                 <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300">Confirm Password</label>
                 <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
             </div>
             <div class="mb-4">
                 <label for="role" class="block text-gray-700 dark:text-gray-300">Role</label>
                 <select name="role" id="role" class="w-full border rounded p-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white" required>
                     @foreach ($roles as $role)
                         <option value="{{ $role->name }}">{{ $role->name }}</option>
                     @endforeach
                 </select>
             </div>
             <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Create</button>
         </form>
     </div>
     @endsection