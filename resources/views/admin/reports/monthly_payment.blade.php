@extends('admin.index')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>Payment Analytics Report</h3>
                <p class="text-muted mb-0">
                    Comprehensive monthly payment analytics with detailed breakdowns.
                </p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Back to Reports
            </a>
        </div>

        {{-- Filter --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Month</label>
                <select id="monthSelect" class="form-select">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Year</label>
                <input type="number" id="yearSelect" class="form-control"
                       min="2000" max="2100" value="{{ $year }}">
            </div>
            
                        
            <div class="col-md-3 d-flex align-items-end">
                <button id="exportPdfBtn" class="btn btn-success w-100" 
                        onclick="window.open('{{ route('admin.reports.paymentAnalytics') }}?export=pdf&month=' + document.getElementById('monthSelect').value + '&year=' + document.getElementById('yearSelect').value, '_blank')">
                    <i class="fas fa-file-pdf me-2"></i>Generate PDF
                </button>
            </div>
        </div>

        {{-- Monthly Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Total Payment Revenue</p>
                    <h4 class="mb-0 text-primary">₱{{ number_format($monthlySummary['total_revenue'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Total Payment Transactions</p>
                    <h4 class="mb-0 text-info">{{ number_format($monthlySummary['total_orders']) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Average Payment Value</p>
                    <h4 class="mb-0 text-success">₱{{ number_format($monthlySummary['average_order_value'], 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3">
                    <p class="text-muted mb-1">Total Customers</p>
                    <h4 class="mb-0 text-warning">{{ number_format($monthlySummary['total_customers']) }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Daily Payment Trend Chart --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">DAILY PAYMENT TREND</h5>
                            <div class="d-flex gap-2">
                                <input type="date" 
                                       id="dailyDateSelect" 
                                       class="form-control form-control-sm" 
                                       style="width: auto;"
                                       value="{{ request('daily_date') ?? now()->format('Y-m-d') }}">
                                <button id="updateDailyBtn" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="dailySalesChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            {{-- Weekly Payment Trend Chart --}}
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">WEEKLY PAYMENT TREND</h5>
                            <div class="d-flex gap-2">
                                <input type="date" 
                                       id="weeklyStartDate" 
                                       class="form-control form-control-sm" 
                                       style="width: auto;"
                                       placeholder="Start Date"
                                       value="{{ request('weekly_start') ?? now()->startOfWeek()->format('Y-m-d') }}">
                                <input type="date" 
                                       id="weeklyEndDate" 
                                       class="form-control form-control-sm" 
                                       style="width: auto;"
                                       placeholder="End Date"
                                       value="{{ request('weekly_end') ?? now()->endOfWeek()->format('Y-m-d') }}">
                                <button id="updateWeeklyBtn" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklySalesChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        
        {{-- MONTHLY BREAKDOWN Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-warning mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>MONTHLY BREAKDOWN
                    </h4>
                    <div class="d-flex gap-2">
                        <select id="breakdownMonthSelect" class="form-select form-select-sm" style="width: auto;">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ (request('breakdown_month') ?? $month) == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" 
                               id="breakdownYearSelect" 
                               class="form-control form-control-sm" 
                               style="width: 100px;"
                               min="2000" 
                               max="2100" 
                               value="{{ request('breakdown_year') ?? $year }}">
                        <button id="updateBreakdownBtn" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Monthly Payments Table --}}
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payments Made</h5>
                    </div>
                    <div class="card-body">
                        @if($monthlyOrders->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-striped">
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
                                        @foreach($monthlyOrders as $order)
                                            <tr>
                                                <td>{{ $order->payment->PaymentID ?? 'N/A' }}</td>
                                                <td>{{ $order->OrderID }}</td>
                                                <td>{{ $order->customer->CustomerName ?? 'Guest' }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $order->payment->PaymentMode ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">₱{{ number_format($order->payment->PaymentTotal ?? $order->OrderTotalAmount, 2) }}</td>
                                                <td class="text-end">{{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $order->payment->PaymentStatus == 'Completed' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $order->payment->PaymentStatus ?? 'Pending' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary fw-bold">
                                            <td colspan="4">Total Payments</td>
                                            <td class="text-end">₱{{ number_format($monthlyRevenue, 2) }}</td>
                                            <td class="text-end">{{ $monthlyOrders->count() }} transactions</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payments made for this period</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment Method Ranking --}}
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method Ranking</h5>
                    </div>
                    <div class="card-body">
                        @if($paymentModes->isNotEmpty())
                            @php
                                $rankedPaymentModes = $paymentModes->sortByDesc('revenue')->values();
                            @endphp
                            @foreach($rankedPaymentModes as $index => $mode)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="badge {{ $index == 0 ? 'bg-warning' : ($index == 1 ? 'bg-secondary' : ($index == 2 ? 'bg-danger' : 'bg-primary')) }} rounded-circle p-2">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-capitalize">{{ str_replace('_', ' ', ucfirst($mode['mode'])) }}</span>
                                            <span class="badge bg-info">{{ number_format($mode['percentage'], 1) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ $mode['percentage'] }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">₱{{ number_format($mode['revenue'], 2) }}</small>
                                            <small class="text-muted">{{ $mode['count'] }} transactions</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payment methods data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Chart Filter
    document.getElementById('updateDailyBtn').addEventListener('click', function() {
        const dailyDate = document.getElementById('dailyDateSelect').value;
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;
        
        let url = new URL(window.location);
        url.searchParams.set('daily_date', dailyDate);
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        url.searchParams.delete('export');
        url.searchParams.delete('weekly');
        url.searchParams.delete('breakdown_period');
        
        window.location.href = url.toString();
    });
    
    // Weekly Chart Filter
    document.getElementById('updateWeeklyBtn').addEventListener('click', function() {
        const weeklyStartDate = document.getElementById('weeklyStartDate').value;
        const weeklyEndDate = document.getElementById('weeklyEndDate').value;
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;
        
        let url = new URL(window.location);
        url.searchParams.set('weekly_start', weeklyStartDate);
        url.searchParams.set('weekly_end', weeklyEndDate);
        url.searchParams.set('month', month);
        url.searchParams.set('year', year);
        url.searchParams.delete('export');
        url.searchParams.delete('daily_date');
        url.searchParams.delete('weekly');
        url.searchParams.delete('breakdown_month');
        url.searchParams.delete('breakdown_year');
        
        window.location.href = url.toString();
    });
    
    // MONTHLY BREAKDOWN Section Filter
    document.getElementById('updateBreakdownBtn').addEventListener('click', function() {
        const breakdownMonth = document.getElementById('breakdownMonthSelect').value;
        const breakdownYear = document.getElementById('breakdownYearSelect').value;
        
        let url = new URL(window.location);
        url.searchParams.set('breakdown_month', breakdownMonth);
        url.searchParams.set('breakdown_year', breakdownYear);
        url.searchParams.delete('export');
        url.searchParams.delete('daily_date');
        url.searchParams.delete('weekly_start');
        url.searchParams.delete('weekly_end');
        url.searchParams.delete('breakdown_period');
        
        window.location.href = url.toString();
    });
    
    // PDF Export functionality
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;
        
        // Simple URL construction
        const baseUrl = window.location.origin + window.location.pathname;
        const pdfUrl = baseUrl + '?export=pdf&month=' + month + '&year=' + year;
        
        console.log('PDF URL:', pdfUrl); // Debug log
        window.open(pdfUrl, '_blank');
    });
    // Prepare data arrays
    @php
        $dailyLabels = [];
        $dailyRevenueArray = [];
        $dailyTransactionsArray = [];
        
        if($dailySales->isNotEmpty()):
            foreach($dailySales as $day):
                $dailyLabels[] = \Carbon\Carbon::parse($day['date'])->format('M d');
                $dailyRevenueArray[] = $day['revenue'];
                $dailyTransactionsArray[] = $day['orders'];
            endforeach;
        else:
            $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
            for($i = 1; $i <= $daysInMonth; $i++):
                $dailyLabels[] = \Carbon\Carbon::createFromDate($year, $month, $i)->format('M d');
                $dailyRevenueArray[] = 0;
                $dailyTransactionsArray[] = 0;
            endfor;
        endif;
        
        $paymentMethodLabelsArray = [];
        $paymentMethodDataArray = [];
        
        if($paymentModes->isNotEmpty()):
            foreach($paymentModes as $mode):
                $paymentMethodLabelsArray[] = str_replace('_', ' ', ucfirst($mode['mode']));
                $paymentMethodDataArray[] = $mode['revenue'];
            endforeach;
        else:
            $paymentMethodLabelsArray[] = 'No Payments';
            $paymentMethodDataArray[] = 1;
        endif;
    @endphp
    
    const dailyLabels = @json($dailyLabels);
    const dailyRevenue = @json($dailyRevenueArray);
    const dailyTransactions = @json($dailyTransactionsArray);
    const paymentMethodLabels = @json($paymentMethodLabelsArray);
    const paymentMethodData = @json($paymentMethodDataArray);

    @php
        $weeklyData = [0, 0, 0, 0, 0, 0, 0];
        if($dailySales->isNotEmpty()):
            foreach($dailySales as $day):
                $dayOfWeek = \Carbon\Carbon::parse($day['date'])->dayOfWeek;
                $weeklyData[$dayOfWeek] = $day['revenue'];
            endforeach;
        endif;
        
        $valueRanges = [0, 0, 0, 0, 0];
        if($dailySales->isNotEmpty()):
            foreach($dailySales as $day):
                $avgValue = $day['average_order_value'];
                if($avgValue <= 100) $valueRanges[0] += $day['orders'];
                elseif($avgValue <= 500) $valueRanges[1] += $day['orders'];
                elseif($avgValue <= 1000) $valueRanges[2] += $day['orders'];
                elseif($avgValue <= 5000) $valueRanges[3] += $day['orders'];
                else $valueRanges[4] += $day['orders'];
            endforeach;
        endif;
        
        // Weekly data preparation for weekly trend chart
        $weeklyTrendData = [0, 0, 0, 0, 0];
        $weeklyTrendTransactions = [0, 0, 0, 0, 0];
        
        if($dailySales->isNotEmpty()):
            foreach($dailySales as $day):
                $date = \Carbon\Carbon::parse($day['date']);
                $weekOfMonth = ceil($date->day / 7);
                if($weekOfMonth > 5) $weekOfMonth = 5;
                
                $weekIndex = $weekOfMonth - 1;
                $weeklyTrendData[$weekIndex] += $day['revenue'];
                $weeklyTrendTransactions[$weekIndex] += $day['orders'];
            endforeach;
        endif;
    @endphp
    
    const weeklyData = @json($weeklyData);
    const valueRangeData = @json($valueRanges);
    const weeklyTrendData = @json($weeklyTrendData);
    const weeklyTrendTransactions = @json($weeklyTrendTransactions);

    // Daily Payment Trend Chart
    const dailySalesCtx = document.getElementById('dailySalesChart');
    if (dailySalesCtx) {
        new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Daily Payment Revenue',
                    data: dailyRevenue,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.1,
                    fill: true
                }, {
                    label: 'Transactions',
                    data: dailyTransactions,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.1,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Payment Revenue (₱)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Transactions'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    }

    // Weekly Payment Trend Chart
    const weeklySalesCtx = document.getElementById('weeklySalesChart');
    if (weeklySalesCtx) {
        new Chart(weeklySalesCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                datasets: [{
                    label: 'Weekly Payment Revenue',
                    data: weeklyTrendData,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }, {
                    label: 'Weekly Transactions',
                    data: weeklyTrendTransactions,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Payment Revenue (₱)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Transactions'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    }

    
    });
</script>
@endpush
@endsection
