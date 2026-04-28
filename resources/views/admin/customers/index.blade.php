@extends('admin.index')

@section('content')

@isset($customerCount)
    <div class="row g-6 mb-6">
      <div class="col-md-6 col-xl-4">
        <div class="card shadow-sm rounded-3 p-4 h-100">
          <h6 class="mb-2">Total Customers</h6>
          <p class="fs-3 mb-0">{{ $customerCount ?? 0 }}</p>
          <div class="mt-3">
            <a href="{{ route('admin.customers.create') }}" class="btn btn-sm btn-primary me-2">
              Add customer
            </a>
            <a href="{{ route('admin.customers.index') }}" class="text-primary d-inline-flex align-items-center">
              View customers
            </a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-8">
        <div class="card shadow-sm rounded-3 p-4 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Recent Customers</h6>
            <a href="{{ route('admin.customers.index') }}" class="small text-muted">See all</a>
          </div>
          <div class="table-responsive">
            <table class="table table-borderless mb-0">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Code</th>
                  <th>City</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentCustomers ?? collect() as $customer)
                  <tr>
                    <td>{{ $customer->CustomerName }}</td>
                    <td>{{ $customer->CustomerCode }}</td>
                    <td>{{ $customer->CustomerCity ?? 'N/A' }}</td>
                    <td>
                      <a href="{{ route('admin.customers.edit', $customer->CustomerID) }}" class="btn btn-sm btn-outline-secondary">
                        Edit
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-muted">No recent customers yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
@endisset

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
                @forelse($customers as $customer)
                    <tr>

                        <!-- FIX: use CustomerCode instead of ID -->
                        <td>
                            <span class="badge bg-primary">
                                {{ $customer->CustomerCode }}
                            </span>
                        </td>

                        <td>{{ $customer->CustomerName }}</td>
                        <td>{{ $customer->CustomerEmail ?: 'N/A' }}</td>
                        <td>{{ $customer->CustomerContactNumber ?: 'N/A' }}</td>
                        <td>{{ $customer->CustomerCity ?: 'N/A' }}</td>

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
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No customers found. Click "Add Customer" to create your first record.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection
