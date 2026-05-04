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

                            @foreach ($products as $product)
                                <div class="mb-3">
                                    <strong>{{ $product->ProductName }}</strong>
                                    - PHP {{ number_format($product->display_price, 2) }}

                                    <input type="number"
                                           name="items[{{ $product->ProductID }}]"
                                           min="0"
                                           class="form-control mt-1"
                                           value="0">
                                </div>
                            @endforeach
                        </div>

                        <!-- SUMMARY -->
                        <div class="col-lg-5">
                            <div class="bg-light p-4 rounded">
                                <h3>Order Summary</h3>

                                <button type="submit" class="btn btn-primary w-100 mt-3">
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
</body>
</html>