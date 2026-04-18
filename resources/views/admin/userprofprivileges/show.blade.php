@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>User Profile Privilege Details</h2>
    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">User Profile ID</dt>
                <dd class="col-sm-9">{{ $userprofprivilege->UserProfileID }}</dd>
                <dt class="col-sm-3">User Privileges ID</dt>
                <dd class="col-sm-9">{{ $userprofprivilege->UserPrivilegesID }}</dd>
                <dt class="col-sm-3">Updated By</dt>
                <dd class="col-sm-9">{{ $userprofprivilege->UserProfPrivilegesUpdateBy }}</dd>
                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $userprofprivilege->UserProfPrivilegesUpdateDate }}</dd>
            </dl>
            <a href="{{ route('admin.userprofprivileges.edit', $userprofprivilege->UserProfPrivilegesID ?? $userprofprivilege->id ?? '') }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.userprofprivileges.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
