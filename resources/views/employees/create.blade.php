<!-- resources/views/employees/create.blade.php -->
@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">Add Employee</h2>

<form method="POST" action="{{ route('employees.store') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block">Name</label>
        <input type="text" name="name" class="w-full p-2 border rounded" required>
    </div>
    <div>
        <label class="block">Telephone</label>
        <input type="text" name="telephone" class="w-full p-2 border rounded">
    </div>
    <div>
        <label class="block">Monthly Salary</label>
        <input type="number" name="monthly_salary" step="0.01" class="w-full p-2 border rounded" required>
    </div>
    <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
</form>
@endsection
