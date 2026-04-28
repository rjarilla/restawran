@extends('admin.index')

@section('content')

<div class="card shadow-sm border-0">

    <div class="card-body">

        <!-- HEADER -->
        <div class="mb-4">
            <h3 class="mb-0">Add Customer</h3>
            <small class="text-muted">Fill out the form to create a new customer</small>
        </div>

        <!-- FORM -->
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="CustomerName" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="CustomerEmail" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="CustomerContactNumber" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="CustomerAddressLine1" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="CustomerCity" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Province</label>
                    <input type="text" name="CustomerProvince" class="form-control">
                </div>

            </div>

            <!-- BUTTONS -->
            <div class="mt-3">
                <button class="btn btn-success">
                    Save Customer
                </button>

                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </form>

    </div>
</div>

@endsection