@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="mb-1">Orders Overview</h2>
            <p class="text-body-secondary mb-0">Track recent orders and key sales numbers at a glance.</p>
        </div>
        <div class="text-md-end">
            <small class="text-body-secondary d-block">Latest order</small>
            <span class="fw-semibold">
                {{ $latestOrderDate ? \Carbon\Carbon::parse($latestOrderDate)->format('M d, Y') : 'No orders yet' }}
            </span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-body-secondary small text-uppercase">Total Orders</span>
                    <h3 class="mt-2 mb-1">{{ number_format($totalOrders) }}</h3>
                    <p class="mb-0 text-body-secondary small">All recorded purchases</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-body-secondary small text-uppercase">Revenue</span>
                    <h3 class="mt-2 mb-1">PHP {{ number_format($totalRevenue, 2) }}</h3>
                    <p class="mb-0 text-body-secondary small">Total value of all orders</p>
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
                    <h4 class="mb-1">Recent Order Summary</h4>
                    <p class="text-body-secondary mb-0 small">Showing the latest 10 orders.</p>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->OrderID }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</td>
                                <td>{{ $order->customer->CustomerName ?? 'Walk-in / Unknown' }}</td>
                                <td>{{ number_format($order->orderDetails->sum('OrderQuantity')) }}</td>
                                <td>PHP {{ number_format($order->OrderTotalAmount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-body-secondary">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
