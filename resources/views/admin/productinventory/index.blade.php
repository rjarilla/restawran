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
                <th><a href="{{ route('admin.productinventory.index', array_merge(request()->all(), ['sort' => 'ProductBatchID', 'direction' => request('direction') === 'asc' && request('sort') === 'ProductBatchID' ? 'desc' : 'asc'])) }}">Batch ID @if(request('sort') === 'ProductBatchID')<span class="small">({{ request('direction', 'asc') == 'asc' ? '▲' : '▼' }})</span>@endif</a></th>
                <th><a href="{{ route('admin.productinventory.index', array_merge(request()->all(), ['sort' => 'ProductName', 'direction' => request('direction') === 'asc' && request('sort') === 'ProductName' ? 'desc' : 'asc'])) }}">Product Name @if(request('sort') === 'ProductName')<span class="small">({{ request('direction', 'asc') == 'asc' ? '▲' : '▼' }})</span>@endif</a></th>
                <th><a href="{{ route('admin.productinventory.index', array_merge(request()->all(), ['sort' => 'ProductQuantity', 'direction' => request('direction') === 'asc' && request('sort') === 'ProductQuantity' ? 'desc' : 'asc'])) }}">Quantity @if(request('sort') === 'ProductQuantity')<span class="small">({{ request('direction', 'asc') == 'asc' ? '▲' : '▼' }})</span>@endif</a></th>
                <th><a href="{{ route('admin.productinventory.index', array_merge(request()->all(), ['sort' => 'ProductBatchDeliveryDate', 'direction' => request('direction') === 'asc' && request('sort') === 'ProductBatchDeliveryDate' ? 'desc' : 'asc'])) }}">Delivery Date @if(request('sort') === 'ProductBatchDeliveryDate')<span class="small">({{ request('direction', 'asc') == 'asc' ? '▲' : '▼' }})</span>@endif</a></th>
                <th><a href="{{ route('admin.productinventory.index', array_merge(request()->all(), ['sort' => 'ProductBatchExpiry', 'direction' => request('direction') === 'asc' && request('sort') === 'ProductBatchExpiry' ? 'desc' : 'asc'])) }}">Expiry @if(request('sort') === 'ProductBatchExpiry')<span class="small">({{ request('direction', 'asc') == 'asc' ? '▲' : '▼' }})</span>@endif</a></th>
                <th><a href="{{ route('admin.productinventory.index', array_merge(request()->all(), ['sort' => 'ProductReceivedBy', 'direction' => request('direction') === 'asc' && request('sort') === 'ProductReceivedBy' ? 'desc' : 'asc'])) }}">Received By @if(request('sort') === 'ProductReceivedBy')<span class="small">({{ request('direction', 'asc') == 'asc' ? '▲' : '▼' }})</span>@endif</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productinventories as $inventory)
            <tr>
                <td>{{ $inventory->ProductBatchID }}</td>
                <td>{{ $inventory->ProductName }}</td>
                <td>{{ $inventory->ProductQuantityRemaining }} / {{ $inventory->ProductQuantity }}</td>
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
