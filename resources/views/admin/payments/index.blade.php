@extends('admin.index')
@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .font-weight-bold {
        font-weight: 700 !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .text-gray-300 {
        color: #dddddd !important;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Payments Management</h2>
        <div class="text-muted">
            <small>Total Payments: {{ $payments->count() }}</small>
        </div>
    </div>

    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Payment Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                PHP {{ number_format($totalAmountPaid, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Items Sold
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalSoldItems) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalCustomers) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Payment Records</h5>
            </div>
            
            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" 
                               name="search_payment_id" 
                               class="form-control" 
                               placeholder="Search by Payment ID..." 
                               value="{{ $searchPaymentId ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" 
                               name="search_order_id" 
                               class="form-control" 
                               placeholder="Search by Order ID..." 
                               value="{{ $searchOrderId ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <select name="payment_mode" class="form-control">
                            <option value="">All Payment Modes</option>
                            <option value="gcash" {{ $paymentMode == 'gcash' ? 'selected' : '' }}>GCash</option>
                            <option value="cash" {{ $paymentMode == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit_card" {{ $paymentMode == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="bank_transfer" {{ $paymentMode == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cash_on_delivery" {{ $paymentMode == 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            @if($searchPaymentId || $searchOrderId || $paymentMode)
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Preserve existing filters in hidden fields -->
                @if($period)
                    <input type="hidden" name="period" value="{{ $period }}">
                @endif
                @if($dateFrom)
                    <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                @endif
                @if($dateTo)
                    <input type="hidden" name="date_to" value="{{ $dateTo }}">
                @endif
            </form>
            
            <!-- Filter Buttons -->
            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                <a href="{{ route('admin.payments.index', array_merge(['period' => 'today'], $searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" 
                   class="btn btn-sm {{ $period == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Today
                </a>
                <a href="{{ route('admin.payments.index', array_merge(['period' => 'yesterday'], $searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" 
                   class="btn btn-sm {{ $period == 'yesterday' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Yesterday
                </a>
                <a href="{{ route('admin.payments.index', array_merge(['period' => 'last_7_days'], $searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" 
                   class="btn btn-sm {{ $period == 'last_7_days' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Last 7 Days
                </a>
                <a href="{{ route('admin.payments.index', array_merge(['period' => 'week'], $searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" 
                   class="btn btn-sm {{ $period == 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                    This Week
                </a>
                <a href="{{ route('admin.payments.index', array_merge(['period' => 'last_30_days'], $searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" 
                   class="btn btn-sm {{ $period == 'last_30_days' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Last 30 Days
                </a>
                <a href="{{ route('admin.payments.index', array_merge(['period' => 'month'], $searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" 
                   class="btn btn-sm {{ $period == 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                    This Month
                </a>
                @if($period)
                    <a href="{{ route('admin.payments.index', array_merge($searchPaymentId ? ['search_payment_id' => $searchPaymentId] : [], $searchOrderId ? ['search_order_id' => $searchOrderId] : [], $paymentMode ? ['payment_mode' => $paymentMode] : [])) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear Period
                    </a>
                @endif
            </div>
            
            @if($period || $searchPaymentId || $searchOrderId || $paymentMode)
                <small class="text-muted">
                    Showing: 
                    @if($searchPaymentId) Payment ID containing "{{ $searchPaymentId }}" @endif
                    @if($searchOrderId) Order ID containing "{{ $searchOrderId }}" @endif
                    @if($paymentMode) Payment mode: {{ ucfirst(str_replace('_', ' ', $paymentMode)) }} @endif
                    @if($period && ($searchPaymentId || $searchOrderId || $paymentMode)) for @endif
                    @if($period == 'today') Today's payments @endif
                    @if($period == 'yesterday') Yesterday's payments @endif
                    @if($period == 'last_7_days') Last 7 days payments @endif
                    @if($period == 'week') This week's payments @endif
                    @if($period == 'last_30_days') Last 30 days payments @endif
                    @if($period == 'month') This month's payments @endif
                </small>
            @endif
        </div>
        <div class="card-body">
            @if($payments->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No payments found</h5>
                    <p class="text-muted">Payment records will appear here when customers complete orders.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Payment ID</th>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Payment Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        {{ $payment->PaymentID }}
                                    </td>
                                    <td>
                                        {{ $payment->OrderID }}
                                    </td>
                                    <td>
                                        @if($payment->order && $payment->order->customer)
                                            {{ $payment->order->customer->CustomerName }}
                                            <br>
                                            <small class="text-muted">{{ $payment->order->customer->CustomerEmail }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getPaymentMethodBadgeClass($payment->PaymentMode) }}">
                                            {{ ucfirst(str_replace('_', ' ', $payment->PaymentMode)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>PHP {{ number_format($payment->PaymentTotal, 2) }}</strong>
                                        @if($payment->PaymentChange > 0)
                                            <br>
                                            <small class="text-success">Change: PHP {{ number_format($payment->PaymentChange, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php $paymentStatus = $payment->PaymentStatus ?? 'resolved'; @endphp
                                        <span class="badge bg-{{ $paymentStatus === 'resolved' ? 'success' : 'warning' }}">
                                            {{ ucfirst($paymentStatus) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->order)
                                            {{ \Carbon\Carbon::parse($payment->order->OrderDate)->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payments.show', $payment->PaymentID) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.payments.destroy', $payment->PaymentID) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<?php
function getPaymentMethodBadgeClass($method) {
    $classes = [
        'gcash' => 'success',
        'cash' => 'success',
        'credit_card' => 'primary',
        'bank_transfer' => 'info',
        'cash_on_delivery' => 'warning'
    ];
    
    return $classes[$method] ?? 'secondary';
}
?>
@endsection
