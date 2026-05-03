@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="mb-1">Orders Overview</h2>
            <p class="text-body-secondary mb-0">Track orders and update order items when staff need to make changes.</p>
        </div>
        <div class="text-md-end">
            <small class="text-body-secondary d-block">Latest paid order</small>
            <span class="fw-semibold">
                {{ $latestOrderDate ? \Carbon\Carbon::parse($latestOrderDate)->format('M d, Y') : 'No orders yet' }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-body-secondary small text-uppercase">Total Orders</span>
                    <h3 class="mt-2 mb-1">{{ number_format($totalOrders) }}</h3>
                    <p class="mb-0 text-body-secondary small">All recorded orders</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-body-secondary small text-uppercase">Revenue</span>
                    <h3 class="mt-2 mb-1">PHP {{ number_format($totalRevenue, 2) }}</h3>
                    <p class="mb-0 text-body-secondary small">Total value of orders</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-body-secondary small text-uppercase">Average Order</span>
                    <h3 class="mt-2 mb-1">PHP {{ number_format($averageOrderValue, 2) }}</h3>
                    <p class="mb-0 text-body-secondary small">Revenue per order</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-body-secondary small text-uppercase">Items Sold</span>
                    <h3 class="mt-2 mb-1">{{ number_format($itemsSold) }}</h3>
                    <p class="mb-0 text-body-secondary small">Total quantity across order lines</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Order Summary</h4>
                    <p class="text-body-secondary mb-0 small">Use edit to update quantities or remove items.</p>
                </div>
                <div>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">Add Order</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Payment Total</th>
                            <th>Fulfilled By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->OrderID }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</td>
                                <td>
                                    @if($order->customer)
                                        <a href="{{ route('admin.customers.show', $order->customer->CustomerID) }}" class="fw-semibold text-decoration-none">
                                            {{ $order->customer->CustomerName }}
                                        </a>
                                    @else
                                        Walk-in / Unknown
                                    @endif
                                </td>
                                <td>{{ number_format($order->orderDetails->sum('OrderQuantity')) }}</td>
                                <td>PHP {{ number_format($order->OrderTotalAmount, 2) }}</td>
                                <td>
                                    @if($order->payment)
                                        PHP {{ number_format($order->payment->PaymentTotal, 2) }}
                                    @else
                                        <span class="text-body-secondary">No payment yet</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->OrderFulfilledBy === 'PENDING')
                                        <span class="badge bg-secondary">Pending</span>
                                    @else
                                        {{ $order->OrderFulfilledBy }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.edit', $order->OrderID) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.orders.destroy', $order->OrderID) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this order and return its items to inventory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-body-secondary">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
