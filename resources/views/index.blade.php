@extends('admin.index')

@section('content')

@php
    $customers = $customers ?? collect();
    $products = $products ?? collect();
@endphp

<div class="card shadow-sm border-0">

    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Customers</h3>
                <small class="text-muted">Manage customer records</small>
            </div>

            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                + Add Customer
            </a>
        </div>

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Customer Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>City</th>
                        <th>Province</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers ?? [] as $customer)
                        <tr>
                            <td>{{ $customer->CustomerCode ?? 'N/A' }}</td>
                            <td>{{ $customer->CustomerName }}</td>
                            <td>{{ $customer->CustomerEmail }}</td>
                            <td>{{ $customer->CustomerContactNumber }}</td>
                            <td>{{ $customer->CustomerCity }}</td>
                            <td>{{ $customer->CustomerProvince }}</td>

                            <td class="d-flex gap-2">

                                <!-- EDIT -->
                                <a href="{{ route('admin.customers.edit', $customer->CustomerID) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <!-- DELETE -->
                                <form action="{{ route('admin.customers.destroy', $customer->CustomerID) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this customer?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection