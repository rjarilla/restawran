@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Add User</h2>
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="UserName" class="form-label">Username</label>
            <input type="text" class="form-control @error('UserName') is-invalid @enderror" id="UserName" name="UserName" value="{{ old('UserName') }}">
            @error('UserName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="UserPassword" class="form-label">Password</label>
            <input type="password" class="form-control @error('UserPassword') is-invalid @enderror" id="UserPassword" name="UserPassword">
            @error('UserPassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="UserProfileID" class="form-label">Profile</label>
            <select class="form-control @error('UserProfileID') is-invalid @enderror"
                    id="UserProfileID" name="UserProfileID">
                <option value="">-- Select Profile --</option>
                @foreach($profiles as $profile)
                    <option value="{{ $profile->UserProfileID }}"
                        {{ old('UserProfileID') == $profile->UserProfileID ? 'selected' : '' }}>
                        {{ $profile->UserProfileName }}
                    </option>
                @endforeach
            </select>
            @error('UserProfileID')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="UserStatus" class="form-label">Status</label>
            <select class="form-select @error('UserStatus') is-invalid @enderror" id="UserStatus" name="UserStatus">
                <option value="">-- Select Status --</option>
                <option value="Active" {{ old('UserStatus') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ old('UserStatus') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('UserStatus')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
