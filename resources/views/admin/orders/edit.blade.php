@extends('admin.index')
@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Order</h2>
            <p class="text-body-secondary mb-0">{{ $order->OrderID }}</p>
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

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">Order Details</h4>
                    <p class="mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->OrderDate)->format('M d, Y') }}</p>
                    <p class="mb-2">
                        <strong>Customer:</strong>
                        @if($order->customer)
                            <a href="{{ route('admin.customers.show', $order->customer->CustomerID) }}" class="text-decoration-none">
                                {{ $order->customer->CustomerName }}
                            </a>
                        @else
                            Walk-in / Unknown
                        @endif
                    </p>
                    <p class="mb-2"><strong>Order Total:</strong> <span id="side-order-total">PHP {{ number_format($order->OrderTotalAmount, 2) }}</span></p>
                    <p class="mb-2">
                        <strong>Payment Total:</strong>
                        @if($order->payment)
                            <span id="side-payment-total">PHP {{ number_format($order->payment->PaymentTotal, 2) }}</span>
                        @else
                            <span class="text-body-secondary">No payment yet</span>
                        @endif
                    </p>
                    <p class="mb-0">
                        <strong>Fulfilled By:</strong>
                        @if($order->OrderFulfilledBy === 'PENDING')
                            <span class="badge bg-secondary">Pending</span>
                        @else
                            {{ $order->OrderFulfilledBy }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form action="{{ route('admin.orders.update', $order->OrderID) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Items</h4>
                            <strong id="estimated-total">PHP {{ number_format($order->OrderTotalAmount, 2) }}</strong>
                        </div>
                        <p class="text-body-secondary small">Set quantity to 0 to remove an item from the order.</p>
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
                                    @php
                                        $currentItems = $order->orderDetails->keyBy('ProductID');
                                    @endphp
                                    @foreach($products as $product)
                                        @php
                                            $currentQuantity = (int) optional($currentItems->get($product->ProductID))->OrderQuantity;
                                            $maxQuantity = $product->available_quantity + $currentQuantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $product->ProductName }}</td>
                                            <td>{{ $maxQuantity }}</td>
                                            <td>PHP {{ number_format($product->display_price, 2) }}</td>
                                            <td>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="{{ $maxQuantity }}"
                                                    step="1"
                                                    class="form-control item-quantity"
                                                    name="items[{{ $product->ProductID }}]"
                                                    value="{{ old('items.' . $product->ProductID, $currentQuantity) }}"
                                                    data-price="{{ $product->display_price }}"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($order->payment)
                            <div class="border-top pt-3 mt-3">
                                <h5 class="mb-3">Fulfill Order</h5>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-7">
                                        <label for="PaymentMode" class="form-label">Mode of Payment</label>
                                        <select class="form-select" id="PaymentMode" name="PaymentMode">
                                            <option value="">Select payment mode</option>
                                            <option value="cash" {{ old('PaymentMode', $order->payment->PaymentMode) === 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="gcash" {{ old('PaymentMode', $order->payment->PaymentMode) === 'gcash' ? 'selected' : '' }}>GCash</option>
                                            <option value="credit_card" {{ old('PaymentMode', $order->payment->PaymentMode) === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                            <option value="bank_transfer" {{ old('PaymentMode', $order->payment->PaymentMode) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="cash_on_delivery" {{ old('PaymentMode', $order->payment->PaymentMode) === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        @if($order->OrderFulfilledBy === 'PENDING')
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="mark_fulfilled" name="mark_fulfilled" {{ old('mark_fulfilled') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mark_fulfilled">
                                                Mark order as fulfilled
                                            </label>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">Update Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const inputs = document.querySelectorAll('.item-quantity');
        const total = document.getElementById('estimated-total');
        const sideOrderTotal = document.getElementById('side-order-total');
        const sidePaymentTotal = document.getElementById('side-payment-total');

        function updateTotal() {
            let amount = 0;

            inputs.forEach((input) => {
                const max = parseInt(input.max || '0', 10);
                const quantity = Math.min(parseInt(input.value || '0', 10), max);
                input.value = quantity;
                amount += quantity * parseFloat(input.dataset.price || '0');
            });

            const formatted = 'PHP ' + amount.toFixed(2);
            total.textContent = formatted;
            sideOrderTotal.textContent = formatted;

            if (sidePaymentTotal) {
                sidePaymentTotal.textContent = formatted;
            }
        }

        inputs.forEach((input) => input.addEventListener('input', updateTotal));
        updateTotal();
    })();
</script>
@endsection
