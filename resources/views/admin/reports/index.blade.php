@extends('admin.index')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>Customer Purchase Report</h3>
                <p class="text-muted mb-0">Analyze customer buying behavior from completed orders and payments.</p>
            </div>
        </div>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Report Filter</label>
                <select name="filter_type" class="form-select">
                    <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Month</option>
                    <option value="year" {{ $filterType === 'year' ? 'selected' : '' }}>Year</option>
                    <option value="range" {{ $filterType === 'range' ? 'selected' : '' }}>Date Range</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Month</label>
                <input type="number" min="1" max="12" name="month" class="form-control" value="{{ old('month', $month) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Year</label>
                <input type="number" min="2000" max="2100" name="year" class="form-control" value="{{ old('year', $year) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ old('date_from', $dateFrom) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ old('date_to', $dateTo) }}">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100">Run</button>
            </div>
        </form>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Total Orders</p>
                    <h4 class="mb-0">{{ number_format($summary['totalOrders'] ?? 0) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Total Revenue</p>
                    <h4 class="mb-0">₱{{ number_format($summary['totalRevenue'] ?? 0, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Unique Customers</p>
                    <h4 class="mb-0">{{ number_format($summary['uniqueCustomers'] ?? 0) }}</h4>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h5>Top Customers</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Total Spent</th>
                            <th>Transactions</th>
                            <th>Last Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $customer->CustomerName }}</td>
                                <td>₱{{ number_format($customer->TotalSpent, 2) }}</td>
                                <td>{{ $customer->Transactions }}</td>
                                <td>{{ $customer->LastOrderDate ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No customer purchase data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h5>Purchase Details</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Order Total</th>
                            <th>Payment Total</th>
                            <th>Payment Mode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ optional($order->customer)->CustomerName ?? 'Unknown' }}</td>
                                <td>{{ $order->OrderID }}</td>
                                <td>{{ $order->OrderDate }}</td>
                                <td>₱{{ number_format($order->OrderTotalAmount, 2) }}</td>
                                <td>₱{{ number_format(optional($order->payment)->PaymentTotal ?? 0, 2) }}</td>
                                <td>{{ optional($order->payment)->PaymentMode ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No order records found for the selected range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
