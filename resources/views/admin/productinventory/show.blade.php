@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Product Inventory Batch Details</h2>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Batch ID</dt>
                <dd class="col-sm-9">{{ $productinventory->ProductBatchID }}</dd>
                <dt class="col-sm-3">Product ID</dt>
                <dd class="col-sm-9">{{ $productinventory->ProductID }}</dd>
                <dt class="col-sm-3">Quantity</dt>
                <dd class="col-sm-9">{{ $productinventory->ProductQuantity }}</dd>
                <dt class="col-sm-3">Delivery Date</dt>
                <dd class="col-sm-9">{{ $productinventory->ProductBatchDeliveryDate }}</dd>
                <dt class="col-sm-3">Expiry</dt>
                <dd class="col-sm-9">{{ $productinventory->ProductBatchExpiry }}</dd>
                <dt class="col-sm-3">Received By</dt>
                <dd class="col-sm-9">{{ $productinventory->ProductReceivedBy }}</dd>
            </dl>
            <a href="{{ route('admin.productinventory.edit', $productinventory->ProductBatchID) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.productinventory.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
