{{-- sales.invoice.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        @media (prefers-color-scheme: dark) {
            body { background-color: #1a1a3a; color: #ffffff; }
            .invoice-container { background-color: #2d2d5a; color: #e0e0e0; }
            th { background-color: #3a3a6a; color: #ffffff; }
            .header, .total { border-color: #ffffff; }
            .company-info { color: #b0b0b0; }
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #333333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header .logo { width: 100px; height: auto; }
        .header .invoice-info { text-align: right; }
        .header h1 { font-size: 24px; margin: 0; }
        .customer-info { margin-bottom: 20px; }
        .customer-info h2 { font-size: 18px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #dddddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        td { vertical-align: top; }
        .footer { margin-top: 20px; }
        .total { border-top: 2px solid #000000; padding-top: 10px; text-align: right; }
        .company-info { margin-top: 20px; font-size: 12px; color: #666666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ public_path('logo.png') }}" alt="Company Logo" class="logo">
            @else
                <h2>{{ $company['name'] }}</h2>
            @endif
            <div class="invoice-info">
                <h1>INVOICE</h1>
                <p>Invoice #{{ $sale->id }}</p>
                <p>Issue Date: {{ $sale->sale_date->format('F j, Y') }}</p>
                <p>Due Date: {{ $sale->due_date ? $sale->due_date->format('F j, Y') : 'N/A' }}</p>
                <p>Status: <span class="{{ $sale->status == 'paid' ? 'text-green-600' : ($sale->status == 'partially_paid' ? 'text-yellow-600' : ($sale->status == 'overdue' ? 'text-red-600' : 'text-blue-600')) }}">{{ ucfirst(str_replace('_', ' ', $sale->status)) }}</span></p>
            </div>
        </div>

        <div class="customer-info">
            <h2>Bill To:</h2>
            <p>{{ $sale->customer->name ?? 'Unknown Customer' }}</p>
            <p>Phone: {{ $sale->customer->phone ?? 'N/A' }}</p>
            <p>Email: {{ $sale->customer->email ?? 'N/A' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $sale->saleable_type == 'App\Models\Bird' ? ($sale->saleable->breed . ' (' . $sale->saleable->type . ')') : ($sale->saleable ? 'Eggs' : 'Unknown Product') }}</td>
                    <td>{{ ucfirst($sale->product_variant ?? 'N/A') }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>${{ number_format($sale->unit_price, 2) }}</td>
                    <td>${{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        @if($sale->payments->count() > 0)
            <h3>Payment History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('F j, Y') }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                            <td>{{ $payment->notes ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <div class="total">
                <p><strong>Total Amount:</strong> ${{ number_format($sale->total_amount, 2) }}</p>
                <p><strong>Paid Amount:</strong> ${{ number_format($sale->paid_amount, 2) }}</p>
                <p><strong>Balance Due:</strong> ${{ number_format($sale->total_amount - $sale->paid_amount, 2) }}</p>
            </div>
            <div class="company-info">
                <p><strong>{{ $company['name'] }}</strong></p>
                <p>{{ $company['address'] }}</p>
                <p>{{ $company['phone'] }}</p>
                <p>{{ $company['email'] }}</p>
                <p>Payment Terms: Net 7 days</p>
                <p>Thank you for your business!</p>
            </div>
        </div>
    </div>
</body>
</html>