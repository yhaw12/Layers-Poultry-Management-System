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

        <!-- Actions -->
        <div class="actions no-print">
            <a href="javascript:history.back()" class="btn btn-back">← Close Invoice</a>
            <a href="javascript:window.print()" class="btn btn-print">🖨 Print Invoice</a>
        </div>

        @include('sales.invoice_fragment')

    </div>
</body>
</html>
