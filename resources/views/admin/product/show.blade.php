@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Product Details</h2>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $product->ProductID }}</dd>
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $product->ProductName }}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $product->ProductDescription }}</dd>
                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ $product->ProductCategoryID }}</dd>
                <dt class="col-sm-3">Quantity Type</dt>
                <dd class="col-sm-9">{{ $product->ProductQuantityTypeID }}</dd>
                <dt class="col-sm-3">Price</dt>
                <dd class="col-sm-9">{{ $product->ProductPrice }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $product->ProductStatus }}</dd>
                <dt class="col-sm-3">Updated By</dt>
                <dd class="col-sm-9">{{ $product->ProductUpdatedBy }}</dd>
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $product->ProductUpdatedDate }}</dd>
            </dl>
            <a href="{{ route('admin.product.edit', $product->ProductID) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
