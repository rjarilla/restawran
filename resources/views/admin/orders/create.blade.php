@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Add Order</h2>
            <p class="text-body-secondary mb-0">Choose item quantities from the available products list.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">Customer</h4>
                        <div class="mb-3">
                            <label for="CustomerName" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="CustomerName" name="CustomerName" value="{{ old('CustomerName', 'Walk-in Customer') }}">
                        </div>
                        <div class="mb-3">
                            <label for="CustomerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="CustomerEmail" name="CustomerEmail" value="{{ old('CustomerEmail') }}">
                        </div>
                        <div class="mb-3">
                            <label for="CustomerContactNumber" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="CustomerContactNumber" name="CustomerContactNumber" value="{{ old('CustomerContactNumber') }}">
                        </div>
                        <div class="mb-3">
                            <label for="CustomerAddressLine1" class="form-label">Address</label>
                            <input type="text" class="form-control" id="CustomerAddressLine1" name="CustomerAddressLine1" value="{{ old('CustomerAddressLine1') }}">
                        </div>
                        <div class="mb-0">
                            <label for="PaymentMode" class="form-label">Mode of Payment</label>
                            <select class="form-select" id="PaymentMode" name="PaymentMode" required>
                                <option value="">Select payment mode</option>
                                <option value="cash" {{ old('PaymentMode') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="gcash" {{ old('PaymentMode') === 'gcash' ? 'selected' : '' }}>GCash</option>
                                <option value="credit_card" {{ old('PaymentMode') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="bank_transfer" {{ old('PaymentMode') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="cash_on_delivery" {{ old('PaymentMode') === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                            </select>
                            <small class="text-success">Walk-in/admin orders are saved as resolved payments.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Items</h4>
                            <strong id="estimated-total">PHP 0.00</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Available</th>
                                        <th>Price</th>
                                        <th style="width: 140px;">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $product->ProductName }}</td>
                                            <td>{{ $product->available_quantity }}</td>
                                            <td>PHP {{ number_format($product->display_price, 2) }}</td>
                                            <td>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="{{ $product->available_quantity }}"
                                                    step="1"
                                                    class="form-control item-quantity"
                                                    name="items[{{ $product->ProductID }}]"
                                                    value="{{ old('items.' . $product->ProductID, 0) }}"
                                                    data-price="{{ $product->display_price }}"
                                                    @disabled($product->available_quantity <= 0)
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Order</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    (function () {
        const inputs = document.querySelectorAll('.item-quantity');
        const total = document.getElementById('estimated-total');

        function updateTotal() {
            let amount = 0;

            inputs.forEach((input) => {
                const max = parseInt(input.max || '0', 10);
                const quantity = Math.min(parseInt(input.value || '0', 10), max);
                input.value = quantity;
                amount += quantity * parseFloat(input.dataset.price || '0');
            });

            total.textContent = 'PHP ' + amount.toFixed(2);
        }

        inputs.forEach((input) => input.addEventListener('input', updateTotal));
        updateTotal();
    })();
</script>
@endsection
