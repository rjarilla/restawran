@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User Profile List</h2>
        <a href="{{ route('admin.userprofile.create') }}" class="btn btn-primary">Add User Profile</a>
    </div>
    <form method="GET" action="{{ route('admin.userprofile.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search', $query ?? '') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($userprofiles as $profile)
            <tr>
                <td>{{ $profile->UserProfileID }}</td>
                <td>{{ $profile->UserProfileName }}</td>
                <td>
                    <a href="{{ route('admin.userprofile.edit', $profile->UserProfileID) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.userprofile.destroy', $profile->UserProfileID) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $userprofiles->links() }}
    </div>
</div>
@endsection
