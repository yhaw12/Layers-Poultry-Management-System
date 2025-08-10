<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\UserActivityLog;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $payrolls = Payroll::with('employee')
            ->whereBetween('pay_date', [$start, $end])
            ->orderBy('pay_date', 'desc')
            ->paginate(10);

        $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
            ->sum('net_pay');

        return view('payroll.index', compact('payrolls', 'start', 'end', 'totalPayroll'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $payroll = Payroll::create($validated);
                $this->createTransaction('payroll', [
                    'amount' => $validated['amount'],
                    'description' => "Payroll payment for employee ID {$validated['employee_id']}",
                    'reference_id' => $payroll->id,
                    'reference_type' => Payroll::class,
                ]);

                UserActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'created_payroll',
                    'details' => json_encode(['payroll_id' => $payroll->id, 'amount' => $payroll->amount]),
                ]);

                return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully.');
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create payroll.');
        }
    }

    public function show($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        return view('payroll.show', compact('payroll'));
    }

    public function edit($id)
    {
        $payroll = Payroll::findOrFail($id);
        $employees = Employee::all();
        return view('payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_date' => 'required|date',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $netPay = $employee->monthly_salary + ($validated['bonus'] ?? 0) - ($validated['deductions'] ?? 0);

        $payroll->update([
            'employee_id' => $validated['employee_id'],
            'pay_date' => $validated['pay_date'],
            'base_salary' => $employee->monthly_salary,
            'bonus' => $validated['bonus'] ?? 0,
            'deductions' => $validated['deductions'] ?? 0,
            'net_pay' => $netPay,
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll record updated successfully.');
    }

    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Payroll record deleted successfully.');
    }

    public function exportPDF(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', now()->endOfMonth()->toDateString());

        $payrolls = Payroll::with('employee')
            ->whereBetween('pay_date', [$start, $end])
            ->get();

        $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
            ->sum('net_pay');

        $pdf = Pdf::loadView('payroll.pdf', compact('payrolls', 'start', 'end', 'totalPayroll'));
        return $pdf->download('payroll_report_' . now()->format('Ymd') . '.pdf');
    }

    public function generateMonthly(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $payDate = now()->endOfMonth()->toDateString();

        $employees = Employee::all();

        DB::transaction(function () use ($employees, $payDate) {
            foreach ($employees as $employee) {
                $exists = Payroll::where('employee_id', $employee->id)
                    ->whereMonth('pay_date', substr($payDate, 5, 2))
                    ->whereYear('pay_date', substr($payDate, 0, 4))
                    ->exists();

                if (!$exists) {
                    Payroll::create([
                        'employee_id' => $employee->id,
                        'pay_date' => $payDate,
                        'base_salary' => $employee->monthly_salary,
                        'bonus' => 0,
                        'deductions' => 0,
                        'net_pay' => $employee->monthly_salary,
                        'status' => 'pending',
                        'notes' => 'Auto-generated for ' . $payDate,
                    ]);
                }
            }
        });

        return redirect()->route('payroll.index')->with('success', 'Monthly payroll generated successfully.');
    }
}


