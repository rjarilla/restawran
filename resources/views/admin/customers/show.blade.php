@extends('admin.index')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Customer Details</h2>
            <p class="text-body-secondary mb-0">{{ $customer->CustomerCode ?? 'No customer code' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Back</a>
            <a href="{{ route('admin.customers.edit', $customer->CustomerID) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th>Customer Code</th>
                            <td>{{ $customer->CustomerCode ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $customer->CustomerName }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $customer->CustomerEmail ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Contact Number</th>
                            <td>{{ $customer->CustomerContactNumber ?: 'N/A' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th>Address</th>
                            <td>{{ $customer->CustomerAddressLine1 ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>City</th>
                            <td>{{ $customer->CustomerCity ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Province</th>
                            <td>{{ $customer->CustomerProvince ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $customer->CustomerUpdateDate ? \Carbon\Carbon::parse($customer->CustomerUpdateDate)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
