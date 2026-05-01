@extends('admin.index')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>All Reports</h3>
                <p class="text-muted mb-0">Access various reports and analytics.</p>
            </div>
        </div>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="reportsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab" aria-controls="customer" aria-selected="true">
                    Customer Purchase Report
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="false">
                    Inventory Movement Report
                </button>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content mt-4" id="reportsTabContent">
            <!-- Customer Purchase Report Tab -->
            <div class="tab-pane fade show active" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4>Customer Purchase Report</h4>
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
                    <h5>Daily Sales Report</h5>
                    <div class="card border-0 shadow-sm p-3">
                        <div id="dailySalesChart" style="min-height: 350px;"></div>
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

            <!-- Inventory Movement Report Tab -->
            <div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4>Inventory Movement Report</h4>
                        <p class="text-muted mb-0">Track product stock movements and inventory levels.</p>
                    </div>
                </div>

                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th>Stock In</th>
                                <th>Stock Out (Sales)</th>
                                <th>Current Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->ProductName }}</td>
                                    <td>{{ $product->ProductDescription }}</td>
                                    <td>{{ $product->total_in }}</td>
                                    <td>{{ $product->total_out }}</td>
                                    <td>{{ $product->current_stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No inventory data found for selected dates.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($dailySales);
    
    if (chartData && chartData.length > 0 && typeof ApexCharts !== 'undefined') {
        const dates = chartData.map(item => item.date);
        const totals = chartData.map(item => item.total);
        const averages = chartData.map(item => item.average);

        const options = {
            series: [{
                name: 'Total Revenue',
                type: 'column',
                data: totals
            }, {
                name: 'Average Revenue',
                type: 'line',
                data: averages
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [0, 4]
            },
            colors: ['#0d6efd', '#20c997'],
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels: dates,
            xaxis: {
                type: 'category'
            },
            yaxis: [{
                title: {
                    text: 'Total Revenue (₱)',
                },
            }, {
                opposite: true,
                title: {
                    text: 'Average Revenue (₱)'
                }
            }],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "₱" + val.toFixed(2);
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#dailySalesChart"), options);
        chart.render();
    } else if (document.querySelector("#dailySalesChart")) {
        document.querySelector("#dailySalesChart").innerHTML = '<div class="text-center text-muted p-4">No daily sales data available for the selected period.</div>';
    }
});
</script>
@endsection
