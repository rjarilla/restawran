@extends('admin.index')
@section('content')
<div class="container mt-4">
    <h2>Reports</h2>
    <p>This is the default Reports page. Implement reporting features here.</p>
    <ul class="list-group mt-4">
        <li class="list-group-item">
            <a href="{{ route('admin.reports.inventory_movement') }}">Inventory Movement Report</a>
        </li>
    </ul>
</div>
@endsection
