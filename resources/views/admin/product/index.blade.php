@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Product List</h2>
        <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add Product</a>
    </div>
    <form method="GET" action="{{ route('admin.product.index') }}" class="mb-3">
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
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Quantity Type</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->ProductID }}</td>
                <td>{{ $product->ProductName }}</td>
                <td>{{ $product->ProductDescription }}</td>
                <td>{{ $product->ProductCategoryID }}</td>
                <td>{{ $product->ProductQuantityTypeID }}</td>
                <td>{{ $product->ProductPrice }}</td>
                <td>{{ $product->ProductStatus }}</td>
                <td>
                    <a href="{{ route('admin.product.edit', $product->ProductID) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.product.destroy', $product->ProductID) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection
