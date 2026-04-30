@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Add Product</h2>
    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="ProductName" class="form-label">Name</label>
            <input type="text" class="form-control @error('ProductName') is-invalid @enderror" id="ProductName" name="ProductName" value="{{ old('ProductName') }}">
            @error('ProductName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductDescription" class="form-label">Description</label>
            <textarea class="form-control @error('ProductDescription') is-invalid @enderror" id="ProductDescription" name="ProductDescription">{{ old('ProductDescription') }}</textarea>
            @error('ProductDescription')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductPrice" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control @error('ProductPrice') is-invalid @enderror" id="ProductPrice" name="ProductPrice" value="{{ old('ProductPrice') }}">
            @error('ProductPrice')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
