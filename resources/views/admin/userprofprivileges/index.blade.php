@extends('admin.index')
@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User Profile Privileges List</h2>
        <a href="{{ route('admin.userprofprivileges.create') }}" class="btn btn-primary">Add Privilege</a>
    </div>

    <form method="GET" action="{{ route('admin.userprofprivileges.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-5">
                <select name="search_profile" class="form-select">
                    <option value="">-- All User Profiles --</option>
                    @foreach($userProfiles as $profile)
                        <option value="{{ $profile->UserProfileID }}"
                            {{ request('search_profile') == $profile->UserProfileID ? 'selected' : '' }}>
                            {{ $profile->UserProfileName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <select name="search_privilege" class="form-select">
                    <option value="">-- All Privileges --</option>
                    @foreach($privileges as $priv)
                        <option value="{{ $priv->LookupID }}"
                            {{ request('search_privilege') == $priv->LookupID ? 'selected' : '' }}>
                            {{ $priv->LookupValue }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-outline-secondary w-100" type="submit">Search</button>
                <a href="{{ route('admin.userprofprivileges.index') }}" class="btn btn-outline-danger w-100">Clear</a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>User Profile</th>
                <th>Privilege</th>
                <th>Updated Date and Time</th>
                <th>Updated By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($userprofprivileges as $priv)
            <tr>
                <td>{{ $userProfiles->firstWhere('UserProfileID', $priv->UserProfileID)->UserProfileName ?? $priv->UserProfileID }}</td>
                <td>{{ $privileges->firstWhere('LookupID', $priv->UserPrivilegesID)->LookupValue ?? $priv->UserPrivilegesID }}</td>
                <td>{{ $priv->UserProfPrivilegesUpdateDate }}</td>
                <td>{{ $priv->updatedByUser->UserName ?? 'N/A'}}</td>
                <td>
                    <a href="{{ route('admin.userprofprivileges.edit', [
                            'profile'   => $priv->UserProfileID,
                            'privilege' => $priv->UserPrivilegesID
                        ]) }}"
                        class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('admin.userprofprivileges.destroy', [
                            'profile'   => $priv->UserProfileID,
                            'privilege' => $priv->UserPrivilegesID
                        ]) }}"
                        method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $userprofprivileges->appends(request()->only(['search_profile', 'search_privilege']))->links() }}
    </div>
</div>
@endsection
