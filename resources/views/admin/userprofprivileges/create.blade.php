@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Add User Profile Privilege</h2>
    <form action="{{ route('admin.userprofprivileges.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="UserProfileID" class="form-label">User Profile</label>
            <select class="form-select @error('UserProfileID') is-invalid @enderror" id="UserProfileID" name="UserProfileID">
                <option value="">-- Select User Profile --</option>
                @foreach($userProfiles as $profile)
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
            <label for="UserPrivilegesID" class="form-label">Privilege</label>
            <select class="form-select @error('UserPrivilegesID') is-invalid @enderror" id="UserPrivilegesID" name="UserPrivilegesID">
                <option value="">-- Select Privilege --</option>
                @foreach($privileges as $priv)
                    <option value="{{ $priv->LookupID }}"
                        {{ old('UserPrivilegesID') == $priv->LookupID ? 'selected' : '' }}>
                        {{ $priv->LookupValue }}
                    </option>
                @endforeach
            </select>
            @error('UserPrivilegesID')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.userprofprivileges.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
