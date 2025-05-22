<!-- resources/views/employees/edit.blade.php -->
@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">Edit Employee</h2>

<form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="block">Name</label>
        <input type="text" name="name" value="{{ $employee->name }}" class="w-full p-2 border rounded" required>
    </div>
    <div>
        <label class="block">Telephone</label>
        <input type="text" name="telephone" value="{{ $employee->telephone }}" class="w-full p-2 border rounded">
    </div>
    <div>
        <label class="block">Monthly Salary</label>
        <input type="number" name="monthly_salary" step="0.01" value="{{ $employee->monthly_salary }}" class="w-full p-2 border rounded" required>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
</form>
@endsection
