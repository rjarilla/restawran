@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Edit Product Inventory Batch</h2>
    <form action="{{ route('admin.productinventory.update', $productinventory->ProductBatchID) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="ProductID" class="form-label">Product ID</label>
            <input type="text" class="form-control @error('ProductID') is-invalid @enderror" id="ProductID" name="ProductID" value="{{ old('ProductID', $productinventory->ProductID) }}">
            @error('ProductID')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductQuantity" class="form-label">Quantity</label>
            <input type="number" class="form-control @error('ProductQuantity') is-invalid @enderror" id="ProductQuantity" name="ProductQuantity" value="{{ old('ProductQuantity', $productinventory->ProductQuantity) }}">
            @error('ProductQuantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductBatchDeliveryDate" class="form-label">Delivery Date</label>
            <input type="date" class="form-control @error('ProductBatchDeliveryDate') is-invalid @enderror" id="ProductBatchDeliveryDate" name="ProductBatchDeliveryDate" value="{{ old('ProductBatchDeliveryDate', $productinventory->ProductBatchDeliveryDate) }}">
            @error('ProductBatchDeliveryDate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductBatchExpiry" class="form-label">Expiry</label>
            <input type="date" class="form-control @error('ProductBatchExpiry') is-invalid @enderror" id="ProductBatchExpiry" name="ProductBatchExpiry" value="{{ old('ProductBatchExpiry', $productinventory->ProductBatchExpiry) }}">
            @error('ProductBatchExpiry')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductReceivedBy" class="form-label">Received By</label>
            <input type="text" class="form-control @error('ProductReceivedBy') is-invalid @enderror" id="ProductReceivedBy" name="ProductReceivedBy" value="{{ old('ProductReceivedBy', $productinventory->ProductReceivedBy) }}">
            @error('ProductReceivedBy')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.productinventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
