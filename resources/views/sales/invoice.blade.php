// resources/views/sales/invoice.blade.php

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Invoice #{{ $sale->id }}</h1>
    <p><strong>Customer:</strong> {{ $sale->customer->name }}</p>
    <p><strong>Sale Date:</strong> {{ $sale->sale_date->format('Y-m-d') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $sale->saleable_type == 'App\Models\Bird' ? $sale->saleable->breed . ' (' . $sale->saleable->type . ')' : 'Eggs' }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>{{ number_format($sale->unit_price, 2) }}</td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <p><strong>Total Amount:</strong> {{ number_format($sale->total_amount, 2) }}</p>
</body>
</html>