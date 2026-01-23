<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .invoice-container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        /* Back/Print Buttons */
        .actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
        }

        .btn-back:hover { background: #5a6268; }

        .btn-print {
            background: #007bff;
            color: #fff;
        }

        .btn-print:hover { background: #0056b3; }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #007bff;
        }

        .invoice-info {
            text-align: right;
            font-size: 14px;
            color: #555;
        }

        /* Customer Info */
        .customer-info {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .customer-info h3 {
            margin-bottom: 6px;
            font-size: 16px;
            color: #444;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 14px;
        }

        th, td {
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Totals Box */
        .totals {
            text-align: right;
            margin-top: 20px;
        }

        .totals div {
            margin: 5px 0;
            font-size: 15px;
        }

        .totals strong {
            font-size: 16px;
            color: #007bff;
        }

        .totals-box {
            display: inline-block;
            background: #f1f8ff;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1px solid #cce5ff;
            margin-top: 10px;
        }

        /* Footer */
        .company-info {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            line-height: 1.5;
            text-align: center;
        }

        @media print {
            .actions { display: none; }
            body { background: #fff; }
            .invoice-container { box-shadow: none; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="actions no-print">
            <a href="javascript:history.back()" class="btn btn-back">← Back</a>
            <button onclick="window.print()" class="btn btn-print">🖨 Print Invoice</button>
        </div>

        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <div style="font-size: 0.9em; color: #666; margin-top: 5px;">{{ $company['name'] }}</div>
                <div style="font-size: 0.8em; color: #888;">{{ $company['address'] }}</div>
                <div style="font-size: 0.8em; color: #888;">{{ $company['phone'] }}</div>
            </div>
            <div class="invoice-info">
                <div><strong>Invoice #:</strong> {{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Date:</strong> {{ $sale->sale_date->format('M d, Y') }}</div>
                <div><strong>Status:</strong> 
                    <span style="color: {{ $sale->isPaid() ? 'green' : 'red' }}; font-weight: bold; text-transform: uppercase;">
                        {{ str_replace('_', ' ', $sale->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="customer-info">
            <div>
                <h3>Bill To:</h3>
                <div>{{ $sale->customer->name }}</div>
                <div>{{ $sale->customer->phone ?? 'No Phone' }}</div>
            </div>
        </div>

        <h3>Order Details</h3>
        <table>
            <thead>
                <tr>
                    <th style="text-align: left;">Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if($sale->items->count() > 0)
                    @foreach($sale->items as $item)
                        <tr>
                            <td>
                                {{ $item->saleable_type === 'App\Models\Egg' ? 'Egg Crate' : 'Bird' }}
                                {{-- <span style="font-size: 0.85em; color: #666; margin-left: 5px;">
                                    (Batch: {{ $item->saleable->displayName() ?? '#'.$item->saleable_id }})
                                </span> --}}
                            </td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">₵ {{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align: right;">₵ {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    {{-- Fallback for legacy data --}}
                    <tr>
                        <td>{{ class_basename($sale->saleable_type) }} ({{ $sale->product_variant }})</td>
                        <td style="text-align: center;">{{ $sale->quantity }}</td>
                        <td style="text-align: right;">₵ {{ number_format($sale->unit_price, 2) }}</td>
                        <td style="text-align: right;">₵ {{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <div>Total: <strong>₵ {{ number_format($sale->total_amount, 2) }}</strong></div>
            <div style="color: #28a745;">Paid: <strong>(-) ₵ {{ number_format($sale->paid_amount, 2) }}</strong></div>
            <div class="totals-box">
                <strong>Due: ₵ {{ number_format($sale->balance(), 2) }}</strong>
            </div>
        </div>

        {{-- @if($sale->payments->count() > 0)
            <div style="margin-top: 40px;">
                <h3>Payment History</h3>
                <table style="width: 60%; font-size: 13px;">
                    <thead>
                        <tr><th style="background:#eee;">Date</th><th style="background:#eee;">Method</th><th style="background:#eee; text-align:right;">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($sale->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                <td style="text-align: right;">₵ {{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif --}}

        <div class="company-info">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
