<!DOCTYPE html>
<html>
<head>
    <title>Payroll Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Payroll Report ({{ $start }} to {{ $end }})</h1>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Pay Date</th>
                <th>Base Salary</th>
                <th>Bonus</th>
                <th>Deductions</th>
                <th>Net Pay</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->employee->name }}</td>
                    <td>{{ $payroll->pay_date }}</td>
                    <td>{{ number_format($payroll->base_salary, 2) }}</td>
                    <td>{{ number_format($payroll->bonus, 2) }}</td>
                    <td>{{ number_format($payroll->deductions, 2) }}</td>
                    <td>{{ number_format($payroll->net_pay, 2) }}</td>
                    <td>{{ ucfirst($payroll->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="summary">Total Payroll: {{ number_format($totalPayroll, 2) }}</p>
</body>
</html>