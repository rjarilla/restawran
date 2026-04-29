@extends('admin.layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('admin.customers.edit', $customer->CustomerID) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Customer Code:</th>
                                    <td>{{ $customer->CustomerCode }}</td>
                                </tr>
                                <tr>
                                    <th>Customer Name:</th>
                                    <td>{{ $customer->CustomerName }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $customer->CustomerEmail ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Number:</th>
                                    <td>{{ $customer->CustomerContactNumber ?: 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Address:</th>
                                    <td>{{ $customer->CustomerAddressLine1 ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $customer->CustomerCity ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Province:</th>
                                    <td>{{ $customer->CustomerProvince ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $customer->CustomerUpdateDate ? $customer->CustomerUpdateDate->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection