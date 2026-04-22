<?php $pageTitle = "- Order Now"; ?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <?php include_once resource_path('includes/header.php'); ?>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="{{ url('/') }}" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Restawran</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="{{ url('/') }}" class="nav-item nav-link">Home</a>
                        <a href="{{ route('order.create') }}" class="nav-item nav-link active">Order</a>
                        <a href="{{ url('/admin') }}" class="nav-item nav-link">Admin</a>
                    </div>
                </div>
            </nav>

            <div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container text-center my-5 pt-5 pb-4">
                    <h1 class="display-3 text-white mb-3 animated slideInDown">Build Your Order</h1>
                    <p class="text-white mb-0">Pick your items, add delivery details, and save the order directly into Restawran.</p>
                </div>
            </div>
        </div>

        <div class="container-xxl py-5">
            <div class="container">
                @if ($confirmation)
                    <div class="alert alert-success mb-4">
                        <h5 class="mb-2">Order placed successfully.</h5>
                        <p class="mb-1"><strong>Order ID:</strong> {{ $confirmation['order_id'] }}</p>
                        <p class="mb-1"><strong>Customer:</strong> {{ $confirmation['customer_name'] }}</p>
                        <p class="mb-3"><strong>Total:</strong> PHP {{ $confirmation['total_amount'] }}</p>
                        <ul class="mb-0">
                            @foreach ($confirmation['items'] as $item)
                                <li>{{ $item['name'] }} x {{ $item['quantity'] }} - PHP {{ $item['line_total'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <h5 class="mb-2">We couldn't place the order yet.</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('order.store') }}">
                    @csrf
                    <div class="row g-5">
                        <div class="col-lg-7">
                            <div class="section-title text-start">
                                <h5 class="ff-secondary text-primary fw-normal">Menu</h5>
                                <h2 class="mb-4">Choose Your Items</h2>
                            </div>

                            @if ($products->isEmpty())
                                <div class="alert alert-warning">
                                    No products are available yet. Add products in the admin panel before placing an order.
                                </div>
                            @else
                                <div class="row g-4">
                                    @foreach ($products as $product)
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <img
                                                    src="{{ asset($product->ProductImagePath ?: 'assets/images/products/default.png') }}"
                                                    class="card-img-top"
                                                    alt="{{ $product->ProductName }}"
                                                    style="height: 220px; object-fit: cover;"
                                                >
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title mb-0">{{ $product->ProductName }}</h5>
                                                        <span class="badge bg-primary rounded-pill">PHP {{ number_format($product->display_price, 2) }}</span>
                                                    </div>
                                                    <p class="card-text text-muted flex-grow-1">
                                                        {{ $product->ProductDescription ?: 'Freshly prepared and ready for your order.' }}
                                                    </p>
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-6">
                                                            <label class="form-label mb-1" for="item-{{ $product->ProductID }}">Quantity</label>
                                                            <input
                                                                id="item-{{ $product->ProductID }}"
                                                                type="number"
                                                                min="0"
                                                                step="1"
                                                                class="form-control item-quantity"
                                                                name="items[{{ $product->ProductID }}]"
                                                                value="{{ old('items.' . $product->ProductID, 0) }}"
                                                                data-name="{{ $product->ProductName }}"
                                                                data-price="{{ $product->display_price }}"
                                                            >
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <small class="text-muted d-block">Category</small>
                                                            <strong>{{ $product->ProductCategoryID }}</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-5">
                            <div class="bg-light rounded p-4 p-lg-5 shadow-sm">
                                <div class="section-title text-start">
                                    <h5 class="ff-secondary text-primary fw-normal">Checkout</h5>
                                    <h2 class="mb-4">Customer Details</h2>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerName" name="CustomerName" placeholder="Full Name" value="{{ old('CustomerName') }}">
                                            <label for="CustomerName">Full Name</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="CustomerEmail" name="CustomerEmail" placeholder="Email Address" value="{{ old('CustomerEmail') }}">
                                            <label for="CustomerEmail">Email Address</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerContactNumber" name="CustomerContactNumber" placeholder="Contact Number" value="{{ old('CustomerContactNumber') }}">
                                            <label for="CustomerContactNumber">Contact Number</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerAddressLine1" name="CustomerAddressLine1" placeholder="Address Line 1" value="{{ old('CustomerAddressLine1') }}">
                                            <label for="CustomerAddressLine1">Address Line 1</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerAddressLine2" name="CustomerAddressLine2" placeholder="Address Line 2" value="{{ old('CustomerAddressLine2') }}">
                                            <label for="CustomerAddressLine2">Address Line 2</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerStreet" name="CustomerStreet" placeholder="Street" value="{{ old('CustomerStreet') }}">
                                            <label for="CustomerStreet">Street</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerCity" name="CustomerCity" placeholder="City" value="{{ old('CustomerCity') }}">
                                            <label for="CustomerCity">City</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerProvince" name="CustomerProvince" placeholder="Province" value="{{ old('CustomerProvince') }}">
                                            <label for="CustomerProvince">Province</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="CustomerPostalCode" name="CustomerPostalCode" placeholder="Postal Code" value="{{ old('CustomerPostalCode') }}">
                                            <label for="CustomerPostalCode">Postal Code</label>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Items selected</span>
                                    <strong id="selected-count">0</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Estimated total</span>
                                    <strong id="estimated-total">PHP 0.00</strong>
                                </div>
                                <div id="order-summary" class="small text-muted mb-4">
                                    Add quantities to see your live order summary.
                                </div>

                                <button type="submit" class="btn btn-primary py-3 px-5 w-100" @if($products->isEmpty()) disabled @endif>
                                    Place Order
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php include_once resource_path('includes/footer.php'); ?>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php include_once resource_path('includes/js_includes.php'); ?>
    <script>
        (function () {
            const quantityInputs = document.querySelectorAll('.item-quantity');
            const selectedCount = document.getElementById('selected-count');
            const estimatedTotal = document.getElementById('estimated-total');
            const orderSummary = document.getElementById('order-summary');

            function formatCurrency(amount) {
                return 'PHP ' + amount.toFixed(2);
            }

            function updateSummary() {
                let total = 0;
                let count = 0;
                const lines = [];

                quantityInputs.forEach((input) => {
                    const quantity = parseFloat(input.value || '0');
                    if (quantity > 0) {
                        const price = parseFloat(input.dataset.price || '0');
                        const name = input.dataset.name || 'Item';
                        const lineTotal = quantity * price;

                        total += lineTotal;
                        count += quantity;
                        lines.push(name + ' x ' + quantity + ' - ' + formatCurrency(lineTotal));
                    }
                });

                selectedCount.textContent = count;
                estimatedTotal.textContent = formatCurrency(total);
                orderSummary.innerHTML = lines.length
                    ? lines.join('<br>')
                    : 'Add quantities to see your live order summary.';
            }

            quantityInputs.forEach((input) => {
                input.addEventListener('input', updateSummary);
            });

            updateSummary();
        })();
    </script>
</body>
</html>
