@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Product List</h2>
        <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add Product</a>
    </div>
    <form method="GET" action="{{ route('admin.product.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search Name/Description/ID" value="{{ $query }}">
            </div>
            <div class="col-md-3">
                <select name="sort_by" class="form-control">
                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Created Date</option>
                    <option value="ProductName" {{ $sort_by == 'ProductName' ? 'selected' : '' }}>Name</option>
                    <option value="ProductPrice" {{ $sort_by == 'ProductPrice' ? 'selected' : '' }}>Price</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="sort_order" class="form-control">
                    <option value="asc" {{ $sort_order == 'asc' ? 'selected' : '' }}>Asc</option>
                    <option value="desc" {{ $sort_order == 'desc' ? 'selected' : '' }}>Desc</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
            </div>
        </div>
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
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->ProductID }}</td>
                <td>{{ $product->ProductName }}</td>
                <td>{{ $product->ProductDescription }}</td>
                <td>{{ $product->ProductPrice }}</td>
                <td>
                    <a href="{{ route('admin.product.edit', $product->ProductID) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection
