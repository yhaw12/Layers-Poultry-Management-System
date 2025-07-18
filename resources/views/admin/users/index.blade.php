@extends('layouts.app')

     @section('content')
     <div class="container mx-auto px-4 py-8">
         <h1 class="text-2xl font-bold mb-4">Users</h1>

         @if (session('success'))
             <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                 {{ session('success') }}
             </div>
         @endif

         @if (session('error'))
             <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                 {{ session('error') }}
             </div>
         @endif

         <a href="{{ route('users.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 mb-4 inline-block">Create New User</a>

         <table class="min-w-full bg-white dark:bg-gray-800">
             <thead>
                 <tr>
                     <th class="py-2 px-4 border-b">Name</th>
                     <th class="py-2 px-4 border-b">Email</th>
                     <th class="py-2 px-4 border-b">Role</th>
                     <th class="py-2 px-4 border-b">Actions</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach ($users as $user)
                     <tr>
                         <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                         <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                         <td class="py-2 px-4 border-b">{{ $user->roles->pluck('name')->implode(', ') }}</td>
                         <td class="py-2 px-4 border-b">
                             <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                             <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                             </form>
                         </td>
                     </tr>
                 @endforeach
             </tbody>
         </table>

         {{ $users->links() }}
     </div>
     @endsection