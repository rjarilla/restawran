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

        
        
        {{-- MONTHLY BREAKDOWN Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-warning mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>MONTHLY BREAKDOWN - {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
                    </h4>
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
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Basic test to see if JavaScript is working
console.log('Script loading test - monthly payment report');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing filters...'); // Debug log
    
    // Hide the test element to confirm JS is working
    const testElement = document.getElementById('jsTest');
    if (testElement) {
        testElement.style.display = 'none';
        console.log('JavaScript is working - test element hidden');
    }
    
        
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
    // Prepare data for monthly payments chart
    @php
        $monthlyChartLabels = [];
        $monthlyChartPayments = [];
        $monthlyChartRevenue = [];
        
        if($monthlyPaymentsData->isNotEmpty()):
            foreach($monthlyPaymentsData as $day):
                $monthlyChartLabels[] = \Carbon\Carbon::parse($day['date'])->format('M d');
                $monthlyChartPayments[] = $day['payments'];
                $monthlyChartRevenue[] = $day['revenue'];
            endforeach;
        else:
            $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
            for($i = 1; $i <= $daysInMonth; $i++):
                $monthlyChartLabels[] = \Carbon\Carbon::createFromDate($year, $month, $i)->format('M d');
                $monthlyChartPayments[] = 0;
                $monthlyChartRevenue[] = 0;
            endfor;
        endif;
    @endphp
    
    const monthlyChartLabels = @json($monthlyChartLabels);
    const monthlyChartPayments = @json($monthlyChartPayments);
    const monthlyChartRevenue = @json($monthlyChartRevenue);

    
    
    });
</script>
@endpush
@endsection
