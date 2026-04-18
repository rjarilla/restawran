@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>User Profile Details</h2>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $userprofile->UserProfileID }}</dd>
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $userprofile->UserProfileName }}</dd>
                <dt class="col-sm-3">Updated By</dt>
                <dd class="col-sm-9">{{ $userprofile->UserProfileUpdateBy }}</dd>
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $userprofile->UserProfileUpdateDate }}</dd>
            </dl>
            <a href="{{ route('admin.userprofile.edit', $userprofile->UserProfileID) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.userprofile.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
