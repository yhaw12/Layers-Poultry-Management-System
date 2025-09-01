<?php

// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::paginate(10);
        $payrollTotal = Employee::sum('monthly_salary');
        return view('employees.index', compact('employees', 'payrollTotal'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
        ]);

        Employee::create($request->all());
        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
        ]);

        $employee->update($request->all());
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
            try {
                $employee = Employee::findorFail($id);
                $employee->delete();

                UserActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'deleted_employee',
                'details' => "Deleted employee {$employee->name} record",
             ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Employee deleted successfully.'
                    ], 200);
                }

                return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
            } catch (\Exception $e) {
                // Log::error('Failed to delete bird: ' . $e->getMessage());
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to delete bird batch. ' . ($e->getCode() == 23000 ? 'This record is linked to other data.' : 'Please try again.')
                    ], 500);
                }

        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
        }
   }
}
