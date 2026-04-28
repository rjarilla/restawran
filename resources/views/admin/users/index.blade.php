@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User List</h2>
        @if(in_array('ADD_USER', $actions))
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
        @endif
    </div>
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
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
                <th>Username</th>
                <th>Role</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->UserID }}</td>
                <td>{{ $user->UserName }}</td>
                <td>{{ $user->Role }}</td>
                <td>{{ $user->UserUpdateDate }}</td>
                <td>
                    @if (in_array('EDT_USER', $actions))
                        <a href="{{ route('admin.users.edit', $user->UserID) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                    @if (in_array('DEL_USER', $actions))
                        <form action="{{ route('admin.users.destroy', $user->UserID) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    @endif
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
        {{ $users->links() }}
    </div>
</div>
@endsection
