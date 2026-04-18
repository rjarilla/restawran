@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>User Details</h2>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $user->UserID }}</dd>
                <dt class="col-sm-3">Username</dt>
                <dd class="col-sm-9">{{ $user->UserName }}</dd>
                <dt class="col-sm-3">Profile</dt>
                <dd class="col-sm-9">{{ $user->userProfile->UserProfileName ?? '' }}</dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">{{ $user->UserStatus }}</dd>
                <dt class="col-sm-3">Updated By</dt>
                <dd class="col-sm-9">{{ $user->UserUpdateBy }}</dd>
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $user->UserUpdateDate }}</dd>
            </dl>
            <a href="{{ route('admin.users.edit', $user->UserID) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
