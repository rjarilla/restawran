@extends('admin.index')

@section('content')

<h2>Edit Customer</h2>

<div class="card shadow-sm">
    <div class="card-body">

        <form action="{{ route('admin.customers.update', $customer->CustomerID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="CustomerName"
                       value="{{ $customer->CustomerName }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="CustomerEmail"
                       value="{{ $customer->CustomerEmail }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>Contact</label>
                <input type="text" name="CustomerContactNumber"
                       value="{{ $customer->CustomerContactNumber }}"
                       class="form-control">
            </div>

            <div class="mb-3">
                <label>City</label>
                <input type="text" name="CustomerCity"
                       value="{{ $customer->CustomerCity }}"
                       class="form-control">
            </div>

            <button class="btn btn-primary">Update</button>

            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                Back
            </a>

        </form>

    </div>
</div>

@endsection