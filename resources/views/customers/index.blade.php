@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Customers</h1>
    <a href="{{ route('customers.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      + Add Customer
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <table class="min-w-full bg-white shadow rounded">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="px-4 py-2">Name</th>
        <th class="px-4 py-2">Phone</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($customers as $c)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $c->name }}</td>
        <td class="px-4 py-2">{{ $c->phone }}</td>
        <td class="px-4 py-2 space-x-2">
          <a href="{{ route('customers.edit', $c) }}"
             class="text-blue-600 hover:underline">Edit</a>
          <form action="{{ route('customers.destroy', $c) }}"
                method="POST" class="inline"
                onsubmit="return confirm('Delete this customer?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="3" class="px-4 py-2 text-gray-500">No customers yet.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
