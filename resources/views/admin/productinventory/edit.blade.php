@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Edit Product Inventory Batch</h2>
    <form action="{{ route('admin.productinventory.update', $productinventory->ProductBatchID) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="ProductID" class="form-label">Product</label>
            <select class="form-control @error('ProductID') is-invalid @enderror" id="ProductID" name="ProductID">
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->ProductID }}" {{ old('ProductID', $productinventory->ProductID) == $product->ProductID ? 'selected' : '' }}>{{ $product->ProductName }}</option>
                @endforeach
            </select>
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
            <label for="ProductQuantityRemaining" class="form-label">Quantity Remaining</label>
            <input type="number" class="form-control @error('ProductQuantityRemaining') is-invalid @enderror" id="ProductQuantityRemaining" name="ProductQuantityRemaining" value="{{ old('ProductQuantityRemaining', $productinventory->ProductQuantityRemaining) }}">
            @error('ProductQuantityRemaining')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductBatchDeliveryDate" class="form-label">Delivery Date</label>
                <input type="date" class="form-control @error('ProductBatchDeliveryDate') is-invalid @enderror" id="ProductBatchDeliveryDate" name="ProductBatchDeliveryDate" value="{{ old('ProductBatchDeliveryDate', $productinventory->ProductBatchDeliveryDate ? \Carbon\Carbon::parse($productinventory->ProductBatchDeliveryDate)->format('Y-m-d') : '') }}">
            @error('ProductBatchDeliveryDate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="ProductBatchExpiry" class="form-label">Expiry</label>
                <input type="date" class="form-control @error('ProductBatchExpiry') is-invalid @enderror" id="ProductBatchExpiry" name="ProductBatchExpiry" value="{{ old('ProductBatchExpiry', $productinventory->ProductBatchExpiry ? \Carbon\Carbon::parse($productinventory->ProductBatchExpiry)->format('Y-m-d') : '') }}">
            @error('ProductBatchExpiry')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.productinventory.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
