@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lookup List</h2>
        @if (in_array('ADD_LOOKUP', $actions))
            <a href="{{ route('admin.lookup.create') }}" class="btn btn-primary">Add Lookup</a>
        @endif
    </div>
    <form method="GET" action="{{ route('admin.lookup.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search all..." value="{{ request('search', '') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="category_filter" class="form-control" placeholder="Filter by Category" value="{{ request('category_filter', '') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="name_filter" class="form-control" placeholder="Filter by Name" value="{{ request('name_filter', '') }}">
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-select">
                    <option value="date_desc" {{ request('sort_by', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Newest First</option>
                    <option value="date_asc" {{ request('sort_by') == 'date_asc' ? 'selected' : '' }}>Oldest First</option>
                    <option value="category_asc" {{ request('sort_by') == 'category_asc' ? 'selected' : '' }}>Category (A-Z)</option>
                    <option value="category_desc" {{ request('sort_by') == 'category_desc' ? 'selected' : '' }}>Category (Z-A)</option>
                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
            </div>
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
                <td>{{ $lookup->UpdatedByName ?? $lookup->LookupUpdateBy }}</td>
                <td>{{ $lookup->LookupUpdateDate }}</td>
                <td>
                    @if (in_array('EDT_LOOKUP', $actions))
                        <a href="{{ route('admin.lookup.edit', $lookup->LookupID) }}" class="btn btn-sm btn-warning">Edit</a>
                    @endif
                    @if (in_array('DEL_LOOKUP', $actions))
                        <form action="{{ route('admin.lookup.destroy', $lookup->LookupID) }}" method="POST" style="display:inline-block;">
                            @csrf
                        </form>
                    @endif
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
