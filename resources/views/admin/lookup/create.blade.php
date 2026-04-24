@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Add Lookup</h2>
    <form action="{{ route('admin.lookup.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="LookupCategory" class="form-label">Category</label>
            <select class="form-select mb-2" id="LookupCategorySelect" onchange="document.getElementById('LookupCategory').value=this.value">
                <option value="">-- Select Existing Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
            <input type="text" class="form-control mt-2 @error('LookupCategory') is-invalid @enderror" id="LookupCategory" name="LookupCategory" placeholder="Or enter new category" value="{{ old('LookupCategory') }}">
            @error('LookupCategory')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="LookupName" class="form-label">Name</label>
            <select class="form-select mb-2" id="LookupNameSelect" onchange="document.getElementById('LookupName').value=this.value">
                <option value="">-- Select Existing Name --</option>
                @foreach($names as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </select>
            <input type="text" class="form-control mt-2 @error('LookupName') is-invalid @enderror" id="LookupName" name="LookupName" placeholder="Or enter new name" value="{{ old('LookupName') }}">
            @error('LookupName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="LookupValue" class="form-label">Value</label>
            <input type="text" class="form-control @error('LookupValue') is-invalid @enderror" id="LookupValue" name="LookupValue" value="{{ old('LookupValue') }}">
            @error('LookupValue')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.lookup.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
