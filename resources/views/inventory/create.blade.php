 
@extends('layouts.app')
@section('content')
<div class="p-6 bg-white dark:bg-gray-800 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Add Inventory Item</h1>
  <form method="POST" action="{{ route('inventory.store') }}">
    @csrf
    <div class="mb-4">
      <label class="block">Name</label>
      <input name="name" value="{{ old('name') }}"
             class="w-full border p-2 rounded" />
      @error('name')<p class="text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="mb-4">
      <label class="block">SKU</label>
      <input name="sku" value="{{ old('sku') }}" class="w-full border p-2 rounded" />
      @error('sku')<p class="text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="mb-4">
      <label class="block">Quantity</label>
      <input type="number" name="qty" value="{{ old('qty',0) }}" class="w-full border p-2 rounded" />
      @error('qty')<p class="text-red-600">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded">Save</button>
  </form>
</div>
@endsection
