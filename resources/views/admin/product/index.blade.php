@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Product List</h2>
        <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add Product</a>
    </div>
    <form method="GET" action="{{ route('admin.product.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search Name/Description/ID" value="{{ $query }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="category_filter" class="form-control" placeholder="Filter Category" value="{{ $category_filter }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="quantity_filter" class="form-control" placeholder="Filter Quantity Type" value="{{ $quantity_filter }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="status_filter" class="form-control" placeholder="Filter Status" value="{{ $status_filter }}">
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-control">
                    <option value="ProductUpdatedDate" {{ $sort_by == 'ProductUpdatedDate' ? 'selected' : '' }}>Updated Date</option>
                    <option value="ProductName" {{ $sort_by == 'ProductName' ? 'selected' : '' }}>Name</option>
                    <option value="ProductPrice" {{ $sort_by == 'ProductPrice' ? 'selected' : '' }}>Price</option>
                </select>
            </div>
            <div class="col-md-1">
                <select name="sort_order" class="form-control">
                    <option value="asc" {{ $sort_order == 'asc' ? 'selected' : '' }}>Asc</option>
                    <option value="desc" {{ $sort_order == 'desc' ? 'selected' : '' }}>Desc</option>
                </select>
            </div>
        </div>
        <button class="btn btn-outline-secondary mt-2" type="submit">Filter & Sort</button>
    </form>
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
                <td>{{ $product->category_value}}</td>
                <td>{{ $product->quantity_value}}</td>
                <td>{{ $product->ProductPrice }}</td>
                <td>{{ $product->status_value }}</td>
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
