<!DOCTYPE html>
<html>
<head>
    <title>Payment Analytics Report - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
            @top-center {
                content: "Payment Analytics Report - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}";
                font-size: 10pt;
                color: #666;
            }
            @bottom-center {
                content: "Page " counter(page);
                font-size: 10pt;
                color: #666;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12pt;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 20pt;
            color: #007bff;
        }
        
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 16pt;
            color: #333;
        }
        
        .header p {
            margin: 0;
            font-size: 10pt;
            color: #666;
        }
        
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 10px;
        }
        
        .card {
            border: 1px solid #ddd;
            padding: 12px;
            flex: 1;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .card h4 {
            margin: 0 0 8px 0;
            font-size: 10pt;
            color: #666;
            font-weight: normal;
        }
        
        .card h3 {
            margin: 0;
            font-size: 14pt;
            color: #007bff;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section h3 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 14pt;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 9pt;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                padding: 10px;
                font-size: 10pt;
            }
            
            .header h1 {
                font-size: 18pt;
            }
            
            .header h2 {
                font-size: 14pt;
            }
            
            .card h3 {
                font-size: 12pt;
            }
            
            .card h4 {
                font-size: 9pt;
            }
            
            table {
                font-size: 9pt;
            }
            
            th, td {
                padding: 4px 6px;
            }
            
            .section {
                margin-bottom: 20px;
            }
            
            .section h3 {
                font-size: 12pt;
            }
            
            .footer {
                margin-top: 20px;
                font-size: 8pt;
            }
            
            /* Avoid page breaks inside important elements */
            .summary-cards, .card, .section, table {
                page-break-inside: avoid;
            }
            
            /* Ensure tables don't break across pages if possible */
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Analytics Report</h1>
        <h2>{{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</h2>
        <p>Generated on: {{ now()->format('F d, Y H:i') }}</p>
    </div>

    <div class="summary-cards">
        <div class="card">
            <h4>Total Payment Revenue</h4>
            <h3>₱{{ number_format($monthlySummary['total_revenue'], 2) }}</h3>
        </div>
        <div class="card">
            <h4>Total Payment Transactions</h4>
            <h3>{{ number_format($monthlySummary['total_orders']) }}</h3>
        </div>
        <div class="card">
            <h4>Average Payment Value</h4>
            <h3>₱{{ number_format($monthlySummary['average_order_value'], 2) }}</h3>
        </div>
        <div class="card">
            <h4>Total Customers</h4>
            <h3>{{ number_format($monthlySummary['total_customers']) }}</h3>
        </div>
    </div>

    <div class="section">
        <h3>Payment Methods Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-end">Transactions</th>
                    <th class="text-end">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @if($paymentModes->isNotEmpty())
                    @foreach($paymentModes as $mode)
                        <tr>
                            <td>{{ str_replace('_', ' ', ucfirst($mode['mode'])) }}</td>
                            <td class="text-end">₱{{ number_format($mode['revenue'], 2) }}</td>
                            <td class="text-end">{{ $mode['count'] }}</td>
                            <td class="text-end">{{ number_format($mode['percentage'], 1) }}%</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">No payment methods data available for this period</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Payments Made</h3>
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Payment Method</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Date</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @if($monthlyOrders->isNotEmpty())
                    @foreach($monthlyOrders as $order)
                        <tr>
                            <td>{{ $order->payment->PaymentID ?? 'N/A' }}</td>
                            <td>{{ $order->OrderID }}</td>
                            <td>{{ $order->customer->CustomerName ?? 'Guest' }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $order->payment->PaymentMode ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-end">₱{{ number_format($order->payment->PaymentTotal ?? $order->OrderTotalAmount, 2) }}</td>
                            <td class="text-end">{{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $order->payment->PaymentStatus == 'Completed' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $order->payment->PaymentStatus ?? 'Pending' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center">No payments made for this period</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="4">Total Payments</td>
                    <td class="text-end">₱{{ number_format($monthlyRevenue, 2) }}</td>
                    <td class="text-end">{{ $monthlyOrders->count() }} transactions</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <p>Payment Analytics Report - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}</p>
        <p>Generated by Restawran Payment System</p>
    </div>
</body>
</html>
