@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Edit User Profile Privilege</h2>
    <form action="{{ route('admin.userprofprivileges.update', $userprofprivilege->UserProfPrivilegesID ?? $userprofprivilege->id ?? '') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="UserProfileID" class="form-label">User Profile ID</label>
            <input type="text" class="form-control @error('UserProfileID') is-invalid @enderror" id="UserProfileID" name="UserProfileID" value="{{ old('UserProfileID', $userprofprivilege->UserProfileID) }}">
            @error('UserProfileID')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="UserPrivilegesID" class="form-label">User Privileges ID</label>
            <input type="text" class="form-control @error('UserPrivilegesID') is-invalid @enderror" id="UserPrivilegesID" name="UserPrivilegesID" value="{{ old('UserPrivilegesID', $userprofprivilege->UserPrivilegesID) }}">
            @error('UserPrivilegesID')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.userprofprivileges.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
