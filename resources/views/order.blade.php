<?php $pageTitle = "- Order Now"; ?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <?php include_once resource_path('includes/header.php'); ?>
</head>
<body>
    <div class="container-xxl bg-white p-0">

        <!-- NAVBAR -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="{{ route('home') }}" class="navbar-brand p-0">
                    <h1 class="text-primary m-0">
                        <i class="fa fa-utensils me-3"></i>Restawran
                    </h1>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="{{ route('home') }}" class="nav-item nav-link">Home</a>
                        <a href="{{ route('order.create') }}" class="nav-item nav-link active">Order</a>
                        <a href="{{ url('/admin') }}" class="nav-item nav-link">Admin</a>
                    </div>
                </div>
            </nav>

            <!-- HERO -->
            <div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container text-center my-5 pt-5 pb-4">
                    <h1 class="display-3 text-white mb-3">Build Your Order</h1>
                    <p class="text-white mb-0">
                        Pick your items and place your order directly into Restawran.
                    </p>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="container-xxl py-5">
            <div class="container">

                @if ($confirmation)
                    <div class="alert alert-success mb-4">
                        <h5>Order placed successfully.</h5>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('order.store') }}">
                    @csrf

                    <div class="row g-5">

                        <!-- PRODUCTS -->
                        <div class="col-lg-7">
                            <h2 class="mb-4">Choose Your Items</h2>

                            <!-- Category Tabs -->
                            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                                @foreach($categories as $index => $category)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link @if($index === 0) active @endif" 
                                                id="pills-{{ $category->LookupID }}-tab" 
                                                data-bs-toggle="pill" 
                                                data-bs-target="#pills-{{ $category->LookupID }}" 
                                                type="button" 
                                                role="tab" 
                                                aria-controls="pills-{{ $category->LookupID }}" 
                                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $category->LookupValue }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                @foreach($categories as $index => $category)
                                    <div class="tab-pane fade @if($index === 0) show active @endif" 
                                         id="pills-{{ $category->LookupID }}" 
                                         role="tabpanel" 
                                         aria-labelledby="pills-{{ $category->LookupID }}-tab">
                                        
                                        @php
                                            $categoryProducts = $groupedProducts->get($category->LookupID, collect())
                                                ->sortByDesc('available_quantity');
                                        @endphp

                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h3 class="mb-0">Category {{ $category->LookupValue }}</h3>
                                            <small class="text-muted">Available items appear first</small>
                                        </div>

                                        @if($categoryProducts->isEmpty())
                                            <p class="text-muted">No items available in this category.</p>
                                        @else
                                            <div class="row g-4">
                                                @foreach ($categoryProducts as $product)
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 shadow-sm overflow-hidden {{ $product->available_quantity <= 0 ? 'opacity-75' : '' }}">
                                                            <div class="position-relative">
                                                                @if($product->available_quantity <= 0)
                                                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.7); z-index: 2;">
                                                                        <span class="badge bg-danger fs-5 px-3 py-2 shadow">SOLD OUT</span>
                                                                    </div>
                                                                @endif
                                                                @if($product->ProductImagePath)
                                                                    <img src="{{ asset($product->ProductImagePath) }}" class="card-img-top" alt="{{ $product->ProductName }}" style="height: 200px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                                        <i class="fa fa-utensils fa-3x text-muted"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <h5 class="card-title fw-bold mb-0 me-2">{{ $product->ProductName }}</h5>
                                                                    <span class="badge bg-primary rounded-pill px-3 py-2">PHP {{ number_format($product->display_price, 2) }}</span>
                                                                </div>
                                                                
                                                                <p class="card-text text-muted small mb-4">
                                                                    {{ \Illuminate\Support\Str::limit($product->ProductDescription, 100) }}
                                                                </p>
                                                                
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <span class="text-muted small">Available stock</span>
                                                                    <span class="fw-bold text-success">{{ $product->available_quantity }}</span>
                                                                </div>
                                                                
                                                                <div class="quantity-control">
                                                                    <label class="form-label small fw-bold text-muted mb-1">Quantity</label>
                                                                     <input type="number"
                                                                           id="qty-{{ $product->ProductID }}"
                                                                           name="items[{{ $product->ProductID }}]"
                                                                           data-name="{{ $product->ProductName }}"
                                                                           data-price="{{ $product->display_price }}"
                                                                           min="0"
                                                                           max="{{ $product->available_quantity }}"
                                                                           class="form-control mb-2 text-center qty-input"
                                                                           value="{{ old('items.'.$product->ProductID, 0) }}"
                                                                           {{ $product->available_quantity <= 0 ? 'disabled' : '' }}
                                                                           onchange="updateSummary()">
                                                                    
                                                                    <div class="d-flex gap-2">
                                                                        <button type="button" 
                                                                                class="btn btn-outline-secondary btn-sm px-3" 
                                                                                {{ $product->available_quantity <= 0 ? 'disabled' : '' }}
                                                                                onclick="updateQty('{{ $product->ProductID }}', -1)">
                                                                            <i class="fa fa-minus"></i>
                                                                        </button>
                                                                        <button type="button" 
                                                                                class="btn btn-primary w-100 fw-bold" 
                                                                                {{ $product->available_quantity <= 0 ? 'disabled' : '' }}
                                                                                onclick="updateQty('{{ $product->ProductID }}', 1, {{ $product->available_quantity }})">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- SUMMARY -->
                        <div class="col-lg-5">
                            <div class="bg-light p-5 rounded shadow-sm">
                                <p class="text-primary fw-bold mb-0" style="font-family: 'Pacifico', cursive;">Checkout</p>
                                <h2 class="fw-bold mb-4 position-relative d-inline-block">
                                    Order Summary
                                    <span class="position-absolute start-100 top-50 translate-middle-y ms-3 bg-primary" style="height: 3px; width: 50px;"></span>
                                </h2>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">Items selected</span>
                                    <span class="fw-bold" id="summary-count">0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="text-muted">Estimated total</span>
                                    <span class="fw-bold text-primary" id="summary-total">PHP 0.00</span>
                                </div>

                                <div id="selected-items-list" class="mb-4 small text-muted">
                                    <!-- Dynamic list of items will appear here -->
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase">
                                    Place Order
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <?php include_once resource_path('includes/footer.php'); ?>
    </div>

    <?php include_once resource_path('includes/js_includes.php'); ?>
    
    <script>
        function updateQty(productId, change, max) {
            const input = document.getElementById('qty-' + productId);
            let currentVal = parseInt(input.value) || 0;
            let newVal = currentVal + change;
            
            if (newVal < 0) newVal = 0;
            if (max !== undefined && newVal > max) newVal = max;
            
            input.value = newVal;
            updateSummary();
        }

        function updateSummary() {
            const inputs = document.querySelectorAll('.qty-input');
            let totalCount = 0;
            let totalAmount = 0;
            let itemListHtml = '';

            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if (qty > 0) {
                    const name = input.getAttribute('data-name');
                    const price = parseFloat(input.getAttribute('data-price'));
                    const lineTotal = qty * price;

                    totalCount += qty;
                    totalAmount += lineTotal;
                    itemListHtml += `<div class="mb-1">${name} x ${qty} - PHP ${lineTotal.toFixed(2)}</div>`;
                }
            });

            document.getElementById('summary-count').innerText = totalCount;
            document.getElementById('summary-total').innerText = 'PHP ' + totalAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('selected-items-list').innerHTML = itemListHtml;
        }

        // Initialize summary on page load
        document.addEventListener('DOMContentLoaded', updateSummary);
    </script>
</body>
</html>