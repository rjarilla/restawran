@extends('admin.index')

@section('content')
<div class="container">
    <h1>Inventory Movement Report</h1>
    <form method="GET" action="{{ route('admin.reports.inventory_movement') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
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
                    <td colspan="5">No data found for selected dates.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
