@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Edit User Profile</h2>
    <form action="{{ route('admin.userprofile.update', $userprofile->UserProfileID) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="UserProfileName" class="form-label">Profile Name</label>
            <input type="text" class="form-control @error('UserProfileName') is-invalid @enderror" id="UserProfileName" name="UserProfileName" value="{{ old('UserProfileName', $userprofile->UserProfileName) }}">
            @error('UserProfileName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.userprofile.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
