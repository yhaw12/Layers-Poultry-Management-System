@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md">
  <h1 class="text-2xl font-bold mb-4">{{ isset($customer) ? 'Edit' : 'Add' }} Customer</h1>
  
  <form method="POST"
        action="{{ isset($customer) 
                   ? route('customers.update', $customer) 
                   : route('customers.store') }}"
        class="bg-white p-6 rounded shadow space-y-4">
    @csrf
    @isset($customer)
      @method('PUT')
    @endisset

    <div>
      <label class="block text-gray-700">Name</label>
      <input name="name" type="text"
             value="{{ old('name', $customer->name ?? '') }}"
             class="w-full p-2 border rounded @error('name') border-red-500 @enderror">
      @error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-gray-700">Phone</label>
      <input name="phone" type="text"
             value="{{ old('phone', $customer->phone ?? '') }}"
             class="w-full p-2 border rounded @error('phone') border-red-500 @enderror">
      @error('phone')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
    </div>

    <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      {{ isset($customer) ? 'Update' : 'Save' }}
    </button>
  </form>
</div>
@endsection
