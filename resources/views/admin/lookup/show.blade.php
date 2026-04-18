@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Lookup Details</h2>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $lookup->LookupID }}</dd>
                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ $lookup->LookupCategory }}</dd>
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $lookup->LookupName }}</dd>
                <dt class="col-sm-3">Value</dt>
                <dd class="col-sm-9">{{ $lookup->LookupValue }}</dd>
                <dt class="col-sm-3">Updated By</dt>
                <dd class="col-sm-9">{{ $lookup->LookupUpdateBy }}</dd>
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $lookup->LookupUpdateDate }}</dd>
            </dl>
            <a href="{{ route('admin.lookup.edit', $lookup->LookupID) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.lookup.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
