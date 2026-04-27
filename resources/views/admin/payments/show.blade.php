@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Payment Details</h2>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Payments
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Payment ID:</strong></p>
                            <p><code>{{ $payment->PaymentID }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong></p>
                            <p><code>{{ $payment->OrderID }}</code></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong></p>
                            <p>
                                <span class="badge bg-{{ getPaymentMethodBadgeClass($payment->PaymentMode) }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->PaymentMode)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Date:</strong></p>
                            <p>{{ $payment->order ? \Carbon\Carbon::parse($payment->order->OrderDate)->format('F d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong></p>
                            <p class="h4 text-primary">PHP {{ number_format($payment->PaymentTotal, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Change:</strong></p>
                            <p class="h5 text-success">
                                PHP {{ number_format($payment->PaymentChange, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($payment->order && $payment->order->orderDetails)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->order->orderDetails as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product ? $item->product->ProductName : 'N/A' }}
                                            </td>
                                            <td>{{ $item->OrderQuantity }}</td>
                                            <td>PHP {{ number_format($item->OrderQuantityPrice, 2) }}</td>
                                            <td>PHP {{ number_format($item->OrderItemTotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            @if($payment->order && $payment->order->customer)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong><br>{{ $payment->order->customer->CustomerName }}</p>
                        <p><strong>Email:</strong><br>{{ $payment->order->customer->CustomerEmail }}</p>
                        <p><strong>Phone:</strong><br>{{ $payment->order->customer->CustomerContactNumber }}</p>
                        <p><strong>Address:</strong><br>
                            {{ $payment->order->customer->CustomerAddressLine1 }}
                            @if($payment->order->customer->CustomerAddressLine2)
                                <br>{{ $payment->order->customer->CustomerAddressLine2 }}
                            @endif
                            <br>{{ $payment->order->customer->CustomerCity }}, {{ $payment->order->customer->CustomerProvince }}
                            <br>{{ $payment->order->customer->CustomerPostalCode }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View All Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getPaymentMethodBadgeClass($method) {
    $classes = [
        'gcash' => 'success',
        'credit_card' => 'primary',
        'bank_transfer' => 'info',
        'cash_on_delivery' => 'warning'
    ];
    
    return $classes[$method] ?? 'secondary';
}
?>
@endsection
