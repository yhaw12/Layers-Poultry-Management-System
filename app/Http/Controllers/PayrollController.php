<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Transaction;
use App\Models\UserActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'nullable|date|before_or_equal:end_date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());
            $cacheKey = "payrolls_{$start}_{$end}";

            $payrolls = Cache::remember($cacheKey, 300, function () use ($start, $end) {
                return Payroll::with('employee')
                    ->whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->orderBy('pay_date', 'desc')
                    ->paginate(10);
            });

            $totalPayroll = Cache::remember("total_payroll_{$start}_{$end}", 300, function () use ($start, $end) {
                return Payroll::whereBetween('pay_date', [$start, $end])
                    ->whereNull('deleted_at')
                    ->sum('net_pay') ?? 0;
            });

            return view('payroll.index', compact('payrolls', 'start', 'end', 'totalPayroll'));
        } catch (\Exception $e) {
            Log::error('Failed to load payrolls', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load payrolls.');
        }
    }

    public function create()
    {
        $employees = Employee::whereNull('deleted_at')->get();
        return view('payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
            ]);

            return DB::transaction(function () use ($validated) {
                $payroll = Payroll::create($validated);

                Transaction::create([
                    'type' => 'payroll',
                    'amount' => $validated['amount'],
                    'description' => "Payroll payment for employee ID {$validated['employee_id']}",
                    'reference_id' => $payroll->id,
                    'reference_type' => Payroll::class,
                    'user_id' => Auth::id() ?? 1,
                    'date' => $validated['payment_date'],
                ]);

                UserActivityLog::create([
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'created_payroll',
                    'details' => json_encode(['payroll_id' => $payroll->id, 'amount' => $payroll->amount]),
                ]);

                return redirect()->route('payrolls.index')->with('success', 'Payroll created successfully.');
            });
        } catch (\Exception $e) {
            Log::error('Failed to create payroll', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to create payroll.');
        }
    }

    public function show($id)
    {
        try {
            $payroll = Payroll::with('employee')->whereNull('deleted_at')->findOrFail($id);
            return view('payroll.show', compact('payroll'));
        } catch (\Exception $e) {
            Log::error('Failed to show payroll', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load payroll.');
        }
    }

    public function edit($id)
    {
        try {
            $payroll = Payroll::whereNull('deleted_at')->findOrFail($id);
            $employees = Employee::whereNull('deleted_at')->get();
            return view('payroll.edit', compact('payroll', 'employees'));
        } catch (\Exception $e) {
            Log::error('Failed to load payroll edit', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to load payroll edit page.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $payroll = Payroll::whereNull('deleted_at')->findOrFail($id);

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

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'updated_payroll',
                'details' => "Updated payroll ID {$payroll->id} for employee ID {$validated['employee_id']}",
            ]);

            return redirect()->route('payroll.index')->with('success', 'Payroll record updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update payroll', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update payroll.');
        }
    }

    public function destroy($id)
    {
        try {
            $payroll = Payroll::whereNull('deleted_at')->findOrFail($id);
            $payroll->delete();

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'deleted_payroll',
                'details' => "Deleted payroll ID {$id}",
            ]);

            return redirect()->route('payroll.index')->with('success', 'Payroll record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete payroll', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to delete payroll.');
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $start = $request->input('start_date', now()->subMonths(6)->startOfMonth()->toDateString());
            $end = $request->input('end_date', now()->endOfMonth()->toDateString());

            $payrolls = Payroll::with('employee')
                ->whereBetween('pay_date', [$start, $end])
                ->whereNull('deleted_at')
                ->get();

            $totalPayroll = Payroll::whereBetween('pay_date', [$start, $end])
                ->whereNull('deleted_at')
                ->sum('net_pay') ?? 0;

            $pdf = Pdf::loadView('payroll.pdf', compact('payrolls', 'start', 'end', 'totalPayroll'));
            return $pdf->download('payroll_report_' . now()->format('Ymd') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Failed to export payroll PDF', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to generate payroll PDF.');
        }
    }

    public function generateMonthly(Request $request)
    {
        try {
            $month = $request->input('month', now()->format('Y-m'));
            $payDate = now()->endOfMonth()->toDateString();

            $employees = Employee::whereNull('deleted_at')->get();

            DB::transaction(function () use ($employees, $payDate) {
                foreach ($employees as $employee) {
                    $exists = Payroll::where('employee_id', $employee->id)
                        ->whereMonth('pay_date', substr($payDate, 5, 2))
                        ->whereYear('pay_date', substr($payDate, 0, 4))
                        ->whereNull('deleted_at')
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

            UserActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'generated_monthly_payroll',
                'details' => "Generated monthly payroll for {$month}",
            ]);

            return redirect()->route('payroll.index')->with('success', 'Monthly payroll generated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to generate monthly payroll', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to generate monthly payroll.');
        }
    }
}