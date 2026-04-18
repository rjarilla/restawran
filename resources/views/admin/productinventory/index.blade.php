@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Product Inventory List</h2>
        <a href="{{ route('admin.productinventory.create') }}" class="btn btn-primary">Add Inventory Batch</a>
    </div>
    <form method="GET" action="{{ route('admin.productinventory.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search', $query ?? '') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Batch ID</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Delivery Date</th>
                <th>Expiry</th>
                <th>Received By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productinventories as $inventory)
            <tr>
                <td>{{ $inventory->ProductBatchID }}</td>
                <td>{{ $inventory->ProductID }}</td>
                <td>{{ $inventory->ProductQuantity }}</td>
                <td>{{ $inventory->ProductBatchDeliveryDate }}</td>
                <td>{{ $inventory->ProductBatchExpiry }}</td>
                <td>{{ $inventory->ProductReceivedBy }}</td>
                <td>
                    <a href="{{ route('admin.productinventory.edit', $inventory->ProductBatchID) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.productinventory.destroy', $inventory->ProductBatchID) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $productinventories->links() }}
    </div>
</div>
@endsection
