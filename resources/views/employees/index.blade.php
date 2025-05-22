@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">Employees</h2>

<a href="{{ route('employees.create') }}" class="mb-4 inline-block bg-blue-500 text-white px-4 py-2 rounded">Add Employee</a>

@if (session('success'))
    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<table class="w-full bg-white rounded shadow mb-6">
    <thead class="bg-gray-200 text-left">
        <tr>
            <th class="p-3">Name</th>
            <th class="p-3">Telephone</th>
            <th class="p-3">Salary</th>
            <th class="p-3">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)
            <tr class="border-t">
                <td class="p-3">{{ $employee->name }}</td>
                <td class="p-3">{{ $employee->telephone ?? '-' }}</td>
                <td class="p-3">${{ number_format($employee->monthly_salary, 2) }}</td>
                <td class="p-3 space-x-2">
                    <a href="{{ route('employees.edit', $employee) }}" class="text-blue-600">Edit</a>
                    <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Delete this employee?')" class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mb-4">
    <strong>Total Monthly Payroll:</strong> ${{ number_format($payrollTotal, 2) }}
</div>

{{ $employees->links() }}
@endsection
