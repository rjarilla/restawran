@extends('admin.index')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>Product Sales Performance Report</h3>
                <p class="text-muted mb-0">
                    Evaluates product performance based on sales transactions.
                </p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Back to Reports
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select name="sales_month" class="form-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $salesMonth == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Year</label>
                <input type="number" name="sales_year" class="form-control"
                       min="2000" max="2100" value="{{ $salesYear }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Generate</button>
            </div>
        </form>

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Total Revenue</p>
                    <h4 class="mb-0">₱{{ number_format($salesSummary['totalRevenue'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Units Sold</p>
                    <h4 class="mb-0">{{ number_format($salesSummary['totalUnits']) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Products Sold</p>
                    <h4 class="mb-0">{{ $salesSummary['productCount'] }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Best Seller</p>
                    <h5 class="mb-0 text-truncate" title="{{ $salesSummary['bestSeller'] }}">
                        {{ $salesSummary['bestSeller'] }}
                    </h5>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="mb-3">Revenue by Product (Top 10)</h6>
                    <div id="salesRevenueChart" style="min-height:300px;"></div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="mb-3">Category Performance</h6>
                    <div id="salesCategoryChart" style="min-height:300px;"></div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <h5>Product Breakdown</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                        <th>Share</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesProducts as $i => $product)
                        @php
                            $share = $salesSummary['totalRevenue'] > 0
                                ? round($product->total_revenue / $salesSummary['totalRevenue'] * 100, 1)
                                : 0;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $product->ProductName }}</strong></td>
                            <td class="text-muted"
                                style="max-width:200px; white-space:nowrap;
                                       overflow:hidden; text-overflow:ellipsis;">
                                {{ $product->ProductDescription ?? '—' }}
                            </td>
                            <td>{{ $product->ProductCategory ?? '—' }}</td>
                            <td>₱{{ number_format($product->ProductPrice, 2) }}</td>
                            <td>{{ number_format($product->total_qty) }}</td>
                            <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar bg-primary"
                                             style="width:{{ $share }}%"></div>
                                    </div>
                                    <small>{{ $share }}%</small>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No sales data for the selected month/year.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const salesData     = @json($salesProducts);
    const salesCategory = @json($salesByCategory);

    if (salesData && salesData.length > 0 && typeof ApexCharts !== 'undefined') {

        // Revenue by product (top 10 horizontal bar)
        const top10 = salesData.slice(0, 10);
        new ApexCharts(document.querySelector('#salesRevenueChart'), {
            series: [{ name: 'Revenue', data: top10.map(p => parseFloat(p.total_revenue)) }],
            chart:  { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: top10.map(p => p.ProductName),
                labels: { formatter: v => '₱' + Number(v).toLocaleString() }
            },
            tooltip: { y: { formatter: v => '₱' + v.toFixed(2) } },
            colors: ['#0d6efd']
        }).render();

        // Category donut
        const catLabels = Object.keys(salesCategory);
        const catValues = Object.values(salesCategory).map(v => parseFloat(v));
        if (catLabels.length > 0) {
            new ApexCharts(document.querySelector('#salesCategoryChart'), {
                series: catValues,
                labels: catLabels,
                chart:  { type: 'donut', height: 300, toolbar: { show: false } },
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: v => '₱' + v.toFixed(2) } },
                colors: ['#0d6efd', '#20c997', '#ffc107', '#dc3545', '#6f42c1']
            }).render();
        } else {
            document.querySelector('#salesCategoryChart').innerHTML =
                '<div class="text-center text-muted p-4">No category data.</div>';
        }

    } else {
        document.querySelector('#salesRevenueChart').innerHTML =
            '<div class="text-center text-muted p-4">No sales data for the selected period.</div>';
    }
});
</script>
@endsection
