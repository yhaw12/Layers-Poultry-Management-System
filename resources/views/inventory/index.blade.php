 
@extends('layouts.app')
@section('content')
<div class="p-6 bg-white dark:bg-gray-800 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Inventory Items</h1>
    <a href="{{ route('inventory.create') }}"
       class="bg-blue-600 text-white py-2 px-4 rounded">Add Item</a>
  </div>
  @if(session('success'))
    <div class="mb-4 text-green-600">{{ session('success') }}</div>
  @endif
  <table class="w-full table-auto">
    <thead><tr><th>Name</th><th>SKU</th><th>Qty</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($items as $item)
      <tr>
        <td>{{ $item->name }}</td>
        <td>{{ $item->sku }}</td>
        <td>{{ $item->qty }}</td>
        <td class="space-x-2">
          <a href="{{ route('inventory.edit',$item) }}" class="text-blue-600">Edit</a>
          <form method="POST" action="{{ route('inventory.destroy',$item) }}" class="inline">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
