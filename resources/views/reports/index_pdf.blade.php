```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .container { padding: 20px; }
        h1 { font-size: 20px; margin-bottom: 20px; }
        h2 { font-size: 16px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-4 { margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ ucfirst($type) }} Report</h1>

        @if ($type == 'weekly')
            <h2>Weekly Egg Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Week</th>
                        <th>Total Crates</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['weekly'] as $row)
                        <tr>
                            <td>{{ $row->year }}</td>
                            <td>{{ $row->week }}</td>
                            <td>{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($type == 'monthly')
            <h2>Monthly Egg Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Total Crates</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['monthly'] as $row)
                        <tr>
                            <td>{{ $row->year }}</td>
                            <td>{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                            <td>{{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($type == 'custom')
            @if (isset($data['eggs']) && $data['eggs']->count() > 0)
                <h2>Eggs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date Laid</th>
                            <th>Crates</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['eggs'] as $egg)
                            <tr>
                                <td>{{ $egg->date_laid }}</td>
                                <td>{{ number_format($egg->crates, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if (isset($data['sales']) && $data['sales']->count() > 0)
                <h2>Sales</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['sales'] as $sale)
                            <tr>
                                <td>{{ $sale->sale_date }}</td>
                                <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                                <td>{{ $sale->saleable_type == 'App\Models\Bird' ? ($sale->saleable->breed ?? 'N/A') : 'Egg Batch ' . ($sale->saleable_id ?? 'N/A') }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>${{ number_format($sale->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if (isset($data['expenses']) && $data['expenses']->count() > 0)
                <h2>Expenses</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['expenses'] as $expense)
                            <tr>
                                <td>{{ $expense->date }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>${{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if (isset($data['payrolls']) && $data['payrolls']->count() > 0)
                <h2>Payrolls</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Pay Date</th>
                            <th>Employee</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['payrolls'] as $payroll)
                            <tr>
                                <td>{{ $payroll->pay_date }}</td>
                                <td>{{ $payroll->employee->name ?? 'N/A' }}</td>
                                <td>${{ number_format($payroll->net_pay, 2) }}</td>
                                <td>{{ ucfirst($payroll->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if (isset($data['transactions']) && $data['transactions']->count() > 0)
                <h2>Transactions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['transactions'] as $transaction)
                            <tr>
                                <td>{{ $transaction->date }}</td>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ $transaction->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if (empty($data['eggs']) && empty($data['sales']) && empty($data['expenses']) && empty($data['payrolls']) && empty($data['transactions']))
                <p>No data available for the selected metrics.</p>
            @endif
        @elseif ($type == 'profitability')
            <h2>Profitability Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Bird ID</th>
                        <th>Breed</th>
                        <th>Type</th>
                        <th>Sales</th>
                        <th>Feed Cost</th>
                        <th>Operational Cost</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['profitability'] as $row)
                        <tr>
                            <td>{{ $row->bird_id ?? 'N/A' }}</td>
                            <td>{{ $row->breed }}</td>
                            <td>{{ ucfirst($row->type) }}</td>
                            <td>${{ number_format($row->sales, 2) }}</td>
                            <td>${{ number_format($row->feed_cost, 2) }}</td>
                            <td>${{ number_format($row->operational_cost ?? ($row->total_expenses + $row->total_payroll), 2) }}</td>
                            <td>${{ number_format($row->profit, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @elseif ($type == 'profit-loss')
            <h2>Profit & Loss Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Income</td>
                        <td>${{ number_format($data['profit_loss']['total_income'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Expenses</td>
                        <td>${{ number_format($data['profit_loss']['total_expenses'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Payroll</td>
                        <td>${{ number_format($data['profit_loss']['total_payroll'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Profit/Loss</td>
                        <td>${{ number_format($data['profit_loss']['profit_loss'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <p class="mt-4">Period: {{ $data['profit_loss']['start'] }} to {{ $data['profit_loss']['end'] }}</p>
        @elseif ($type == 'forecast')
            <h2>Financial Forecast</h2>
            <table>
                <thead>
                    <tr>
                        <th>Metric</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Forecasted Income</td>
                        <td>${{ number_format($data['forecast']['forecasted_income'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Forecasted Expenses</td>
                        <td>${{ number_format($data['forecast']['forecasted_expenses'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Forecasted Profit</td>
                        <td>${{ number_format($data['forecast']['forecasted_profit'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
            <p class="mt-4">Based on past 6 months data with 5% income growth and 3% expense growth.</p>
        @endif
    </div>
</body>
</html>
```
{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ucfirst($type) }} Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 25px;
            color: #333;
        }
        header, footer {
            text-align: center;
            margin-bottom: 20px;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        header small {
            color: #7f8c8d;
        }
        h2 {
            margin-top: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #2c3e50;
            color: white;
            text-align: left;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .highlight {
            font-weight: bold;
            background-color: #ecf0f1;
        }
        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
        }
        footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            font-size: 12px;
            color: #7f8c8d;
        }
        .export-btns {
            margin-bottom: 15px;
        }
        .export-btns a {
            display: inline-block;
            padding: 6px 12px;
            margin-right: 8px;
            font-size: 13px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .export-btns a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<header>
    <h1>{{ ucfirst($type) }} Report</h1>
    <small>Generated on: {{ now()->format('d M Y, H:i A') }}</small>
</header>

<div class="export-btns">
    <a href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}">Export as PDF</a>
    <a href="{{ request()->fullUrlWithQuery(['format' => 'excel']) }}">Export as Excel</a>
</div>

{{-- WEEKLY --}}
@if ($type === 'weekly')
    <h2>Weekly Egg Production</h2>
    @if (!empty($data['weekly']) && count($data['weekly']))
        <table>
            <thead>
                <tr><th>Year</th><th>Week</th><th>Total Crates</th></tr>
            </thead>
            <tbody>
                @foreach ($data['weekly'] as $row)
                    <tr>
                        <td>{{ $row->year }}</td>
                        <td>{{ $row->week }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-data">No weekly records available.</p>
    @endif
@endif

{{-- MONTHLY --}}
@if ($type === 'monthly')
    <h2>Monthly Egg Production</h2>
    @if (!empty($data['monthly']) && count($data['monthly']))
        <table>
            <thead>
                <tr><th>Year</th><th>Month</th><th>Total Crates</th></tr>
            </thead>
            <tbody>
                @foreach ($data['monthly'] as $row)
                    <tr>
                        <td>{{ $row->year }}</td>
                        <td>{{ \Carbon\Carbon::create()->month($row->month_num)->format('F') }}</td>
                        <td>{{ $row->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-data">No monthly records available.</p>
    @endif
@endif

{{-- CUSTOM --}}
@if ($type === 'custom')
    @if (!empty($data['eggs']))
        <h2>Eggs</h2>
        <table>
            <thead><tr><th>Date Laid</th><th>Crates</th></tr></thead>
            <tbody>
                @foreach ($data['eggs'] as $egg)
                    <tr>
                        <td>{{ $egg->date_laid }}</td>
                        <td>{{ $egg->crates ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (!empty($data['sales']))
        <h2>Sales</h2>
        <table>
            <thead><tr><th>Date</th><th>Customer</th><th>Item</th><th>Qty</th><th>Total ($)</th></tr></thead>
            <tbody>
                @foreach ($data['sales'] as $sale)
                    <tr>
                        <td>{{ $sale->sale_date }}</td>
                        <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                        <td>{{ $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif

{{-- PROFITABILITY --}}
@if ($type === 'profitability')
    <h2>Profitability by Bird</h2>
    @if (!empty($data['profitability']) && count($data['profitability']))
        <table>
            <thead>
                <tr><th>ID</th><th>Breed</th><th>Sales ($)</th><th>Feed Cost ($)</th><th>Expenses ($)</th><th>Profit ($)</th></tr>
            </thead>
            <tbody>
                @foreach ($data['profitability'] as $row)
                    <tr>
                        <td>{{ $row->bird_id ?? 'N/A' }}</td>
                        <td>{{ $row->breed }}</td>
                        <td>{{ number_format($row->sales, 2) }}</td>
                        <td>{{ number_format($row->feed_cost, 2) }}</td>
                        <td>{{ number_format($row->total_expenses ?? 0, 2) }}</td>
                        <td class="{{ $row->profit < 0 ? 'highlight' : '' }}">
                            {{ number_format($row->profit, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="no-data">No profitability records available.</p>
    @endif
@endif

{{-- PROFIT & LOSS --}}
@if ($type === 'profit-loss' && !empty($data['profit_loss']))
    <h2>Profit & Loss Statement</h2>
    <p><strong>Period:</strong> {{ $data['profit_loss']['start'] }} → {{ $data['profit_loss']['end'] }}</p>
    <table>
        <tr><th>Total Income</th><td>{{ number_format($data['profit_loss']['total_income'], 2) }}</td></tr>
        <tr><th>Total Expenses</th><td>{{ number_format($data['profit_loss']['total_expenses'], 2) }}</td></tr>
        <tr><th>Total Payroll</th><td>{{ number_format($data['profit_loss']['total_payroll'], 2) }}</td></tr>
        <tr class="highlight"><th>Net Profit/Loss</th><td>{{ number_format($data['profit_loss']['profit_loss'], 2) }}</td></tr>
    </table>
@endif

{{-- FORECAST --}}
@if ($type === 'forecast' && !empty($data['forecast']))
    <h2>Financial Forecast</h2>
    <table>
        <tr><th>Forecasted Income</th><td>{{ number_format($data['forecast']['forecasted_income'], 2) }}</td></tr>
        <tr><th>Forecasted Expenses</th><td>{{ number_format($data['forecast']['forecasted_expenses'], 2) }}</td></tr>
        <tr class="highlight"><th>Forecasted Profit</th><td>{{ number_format($data['forecast']['forecasted_profit'], 2) }}</td></tr>
    </table>
@endif

<footer>
    Poultry Management System &copy; {{ date('Y') }}
</footer>
</body>
</html> --}}
