<?php $pageTitle = "- Home"; ?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <?php include_once resource_path('includes/header.php'); ?>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="{{ route('home') }}" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Restawran</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="{{ route('home') }}" class="nav-item nav-link active">Home</a>
                        <a href="{{ route('order.create') }}" class="nav-item nav-link">Order</a>
                        <a href="{{ url('/admin') }}" class="nav-item nav-link">Admin</a>
                    </div>
                </div>
            </nav>

            <div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container my-5 py-5">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6 text-center text-lg-start">
                            <h1 class="display-3 text-white animated slideInLeft">Enjoy fresh meals from Restawran</h1>
                            <p class="text-white animated slideInLeft mb-4 pb-2">
                                Browse today's available items and place an order when you are ready.
                            </p>
                            <a href="{{ route('order.create') }}" class="btn btn-primary py-sm-3 px-sm-5 me-3 animated slideInLeft">Order Now</a>
                        </div>
                        <div class="col-lg-6 text-center text-lg-end overflow-hidden">
                            <img class="img-fluid" src="{{ asset('img/hero.png') }}" alt="Restawran meal">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Menu</h5>
                    <h2 class="mb-5">Available Items</h2>
                </div>

                @if ($products->isEmpty())
                    <div class="alert alert-warning text-center">
                        No products are available yet.
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($products as $product)
                            @php
                                $fallbackImage = 'img/menu-' . ((($loop->iteration - 1) % 8) + 1) . '.jpg';
                                $imagePath = data_get($product, 'ProductImagePath') ?: $fallbackImage;
                                $description = data_get($product, 'ProductDescription') ?: 'Freshly prepared and ready for your order.';
                                $availableQuantity = (int) data_get($product, 'available_quantity', 0);
                            @endphp
                            <div class="col-lg-6">
                                <a href="{{ route('order.create') }}" class="d-block text-decoration-none" style="color: inherit;">
                                    <div class="d-flex align-items-center p-2 rounded" style="transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.backgroundColor='#f8f9fa'; this.style.transform='translateX(5px)';" onmouseout="this.style.backgroundColor='transparent'; this.style.transform='translateX(0)';">
                                        <img class="flex-shrink-0 img-fluid rounded" src="{{ asset($imagePath) }}" alt="{{ $product->ProductName }}" style="width: 88px; height: 88px; object-fit: cover;">
                                        <div class="w-100 d-flex flex-column text-start ps-4">
                                            <h5 class="d-flex justify-content-between border-bottom pb-2">
                                                <span class="text-dark">{{ $product->ProductName }}</span>
                                                <span class="text-primary">PHP {{ number_format((float) data_get($product, 'ProductPrice', 0), 2) }}</span>
                                            </h5>
                                            <small class="fst-italic text-muted">{{ $description }}</small>
                                            <small class="text-muted mt-1">Available stock: {{ $availableQuantity }}</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('order.create') }}" class="btn btn-primary py-3 px-5">Go to Orders</a>
                    </div>
                @endif
            </div>
        </div>

        <?php include_once resource_path('includes/footer.php'); ?>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php include_once resource_path('includes/js_includes.php'); ?>
</body>
</html>
