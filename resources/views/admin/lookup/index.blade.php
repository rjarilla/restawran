@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lookup List</h2>
        <a href="{{ route('admin.lookup.create') }}" class="btn btn-primary">Add Lookup</a>
    </div>
    <form method="GET" action="{{ route('admin.lookup.index') }}" class="mb-3">
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
                <th>Category</th>
                <th>Name</th>
                <th>Value</th>
                <th>Updated By</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lookups as $lookup)
            <tr>
                <td>{{ $lookup->LookupID }}</td>
                <td>{{ $lookup->LookupCategory }}</td>
                <td>{{ $lookup->LookupName }}</td>
                <td>{{ $lookup->LookupValue }}</td>
                <td>{{ $lookup->LookupUpdateBy }}</td>
                <td>{{ $lookup->LookupUpdateDate }}</td>
                <td>
                    <a href="{{ route('admin.lookup.edit', $lookup->LookupID) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.lookup.destroy', $lookup->LookupID) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $lookups->links() }}
    </div>
</div>
@endsection
