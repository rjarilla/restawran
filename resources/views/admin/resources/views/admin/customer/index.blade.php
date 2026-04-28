@extends('admin.index')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h3>Customer List</h3>

        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-3">
            Add Customer
        </a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($customers as $customer)
                    <tr>

                        <!-- FIX: use CustomerCode instead of ID -->
                        <td>
                            <span class="badge bg-primary">
                                {{ $customer->CustomerCode }}
                            </span>
                        </td>

                        <td>{{ $customer->CustomerName }}</td>
                        <td>{{ $customer->CustomerEmail }}</td>
                        <td>{{ $customer->CustomerContactNumber }}</td>
                        <td>{{ $customer->CustomerCity }}</td>

                        <td>
                            <a href="{{ route('admin.customers.edit', $customer->CustomerID) }}"
                               class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('admin.customers.destroy', $customer->CustomerID) }}"
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this customer?')">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>

@endsection