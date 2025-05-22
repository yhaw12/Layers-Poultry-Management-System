@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-6">
    <h2 class="text-2xl font-semibold mb-4">Add Medicine Log</h2>

    <form method="POST" action="{{ route('medicine-logs.store') }}" class="space-y-4">
        @csrf

        <div>
            <label>Medicine Name</label>
            <input name="medicine_name" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label>Type</label>
            <select name="type" class="w-full border p-2 rounded" required>
                <option value="purchase">Purchase</option>
                <option value="consumption">Consumption</option>
            </select>
        </div>

        <div>
            <label>Quantity</label>
            <input name="quantity" type="number" step="0.01" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label>Unit</label>
            <input name="unit" value="ml" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label>Date</label>
            <input name="date" type="date" class="w-full border p-2 rounded" required>
        </div>

        <div>
            <label>Notes</label>
            <textarea name="notes" class="w-full border p-2 rounded"></textarea>
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Save</button>
    </form>
</div>
@endsection
