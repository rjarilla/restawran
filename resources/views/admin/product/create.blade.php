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
            <label for="ProductCategoryID" class="form-label">Category</label>
            <select class="form-select @error('ProductCategoryID') is-invalid @enderror" id="ProductCategoryID" name="ProductCategoryID">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->LookupID }}" {{ old('ProductCategoryID') == $category->LookupID ? 'selected' : '' }}>{{ $category->LookupValue }}</option>
                @endforeach
            </select>
            @error('ProductCategoryID')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductQuantityTypeID" class="form-label">Quantity Type</label>
            <select class="form-select @error('ProductQuantityTypeID') is-invalid @enderror" id="ProductQuantityTypeID" name="ProductQuantityTypeID">
                <option value="">Select Quantity Type</option>
                @foreach($quantityTypes as $type)
                    <option value="{{ $type->LookupID }}" {{ old('ProductQuantityTypeID') == $type->LookupID ? 'selected' : '' }}>{{ $type->LookupValue }}</option>
                @endforeach
            </select>
            @error('ProductQuantityTypeID')
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
        <div class="mb-3">
            <label for="ProductStatus" class="form-label">Status</label>
            <select class="form-select @error('ProductStatus') is-invalid @enderror" id="ProductStatus" name="ProductStatus">
                <option value="">Select Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->LookupID }}" {{ old('ProductStatus') == $status->LookupID ? 'selected' : '' }}>{{ $status->LookupValue }}</option>
                @endforeach
            </select>
            @error('ProductStatus')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductImage" class="form-label">Product Image</label>
            <input type="file" class="form-control @error('ProductImage') is-invalid @enderror" id="ProductImage" name="ProductImage" accept="image/*">
            @error('ProductImage')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @include('admin.product.partials.image_select')
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
