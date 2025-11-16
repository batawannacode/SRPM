<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: monospace;
            font-size: 11px;
            margin: 0;
            padding: 8px;
            color: #000;
        }

        .receipt {
            width: 100%;
            text-align: center;
        }

        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .details {
            text-align: left;
            margin-top: 5px;
            line-height: 1.4;
        }

        .details p {
            margin: 0;
            display: flex;
            justify-content: space-between;
        }

        .amount {
            font-size: 13px;
            font-weight: bold;
            margin-top: 8px;
            text-align: center;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
        }

        .footer {
            margin-top: 8px;
            border-top: 1px dashed #000;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
        }

    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="title">Payment Receipt</div>
            <div>Receipt No: {{ $payment->id }}</div>
            <div>{{ now('Asia/Manila')->format('F j, Y h:i A') }}</div>
        </div>

        <div class="details">
            <p><span>Tenant:</span> <span>{{ $tenant_user->first_name }} {{ $tenant_user->last_name }}</span></p>
            <p><span>Lease ID:</span> <span>#{{ $lease->id }}</span></p>
            <p><span>Method:</span> <span>{{ ucfirst($payment->payment_method) }}</span></p>
            <p><span>Reference:</span> <span>{{ $payment->reference_number }}</span></p>
            <p><span>Account:</span> <span>{{ $payment->account_name }}</span></p>
            <p><span>Account Number:</span> <span>{{ $payment->account_number }}</span></p>
        </div>

        <div class="amount">
            PHP {{ number_format($payment->amount, 2) }}
        </div>

        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>--- {{ config('app.name') }} ---</p>
        </div>
    </div>
</body>
</html>

