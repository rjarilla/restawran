<?php $pageTitle = "- Payment Method"; ?>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <?php include_once resource_path('includes/header.php'); ?>
    <style>
        .payment-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }
        
        .payment-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(254, 161, 22, 0.15);
        }
        
        .payment-card.selected {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            transform: scale(1.01);
        }
        
        .payment-card.selected .payment-icon {
            color: white;
            transform: scale(1.1);
        }
        
        .payment-card.selected .payment-title,
        .payment-card.selected .payment-description {
            color: white;
        }
        
        .payment-icon {
            font-size: 2rem;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .payment-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: #212529;
            transition: color 0.3s ease;
        }
        
        .payment-description {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 0;
            transition: color 0.3s ease;
        }
        
        .order-summary-card {
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        .order-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: background 0.2s ease;
        }
        
        .order-item:hover {
            background: rgba(13, 110, 253, 0.05);
            margin: 0 -12px;
            padding: 8px 12px;
            border-radius: 4px;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .total-section {
            background: var(--primary);
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
        }
        
        .proceed-btn {
            background: var(--primary);
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .proceed-btn:hover {
            background: #e69500;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(254, 161, 22, 0.3);
        }
        
        .back-btn {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-1px);
        }
        
        .customer-info {
            background: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 1px solid #e9ecef;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-icon {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 12px;
            font-size: 1rem;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-weight: 600;
            color: #212529;
            font-size: 0.9rem;
        }
        
        .section-title h5 {
            color: var(--primary) !important;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }
        
        .payment-methods-container {
            animation-delay: 0.1s;
        }
        
        .order-summary-container {
            animation-delay: 0.2s;
        }
        
        /* Compact layout optimizations */
        .payment-card .d-flex {
            align-items: center;
        }
        
        .payment-card .payment-icon {
            margin-right: 12px;
        }
        
        .payment-card .form-check {
            margin-left: auto;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .payment-card {
                padding: 12px;
            }
            
            .payment-icon {
                font-size: 1.5rem;
            }
            
            .customer-info {
                padding: 12px;
            }
            
            .info-icon {
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }
        }
    </style>
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
                        <a href="{{ route('home') }}" class="nav-item nav-link">Home</a>
                        <a href="{{ route('order.create') }}" class="nav-item nav-link">Order</a>
                        <a href="{{ url('/admin') }}" class="nav-item nav-link">Admin</a>
                    </div>
                </div>
            </nav>

            <div class="container-xxl py-3 bg-dark hero-header mb-4">
                <div class="container text-center my-3 pt-3 pb-3">
                    <h1 class="display-4 text-white mb-2 animated slideInDown">Choose Payment Method</h1>
                    <p class="text-white mb-0">Select your preferred payment method to complete your order.</p>
                </div>
            </div>
        </div>

        <div class="container-xxl py-3">
            <div class="container">
                @if ($errors->any())
                    <div class="alert alert-danger mb-3 fade-in-up">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2" style="font-size: 1.2rem;"></i>
                            <div>
                                <h6 class="mb-1">Payment Error</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="section-title text-start fade-in-up mb-3">
                            <h5 class="ff-secondary text-primary fw-normal">Payment Options</h5>
                            <h3 class="mb-3">Select Your Payment Method</h3>
                        </div>

                        <form method="POST" action="{{ route('payment.process') }}" id="paymentForm">
                            @csrf
                            
                            <div class="payment-methods-container fade-in-up">
                                <div class="payment-card" onclick="selectPaymentMethod('gcash')">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="payment-title">GCash</div>
                                            <p class="payment-description">Pay instantly using your GCash wallet</p>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="gcash" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-card" onclick="selectPaymentMethod('credit_card')">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="payment-title">Credit Card</div>
                                            <p class="payment-description">Secure payment with Visa, Mastercard, or Amex</p>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-card" onclick="selectPaymentMethod('bank_transfer')">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="payment-title">Bank Transfer</div>
                                            <p class="payment-description">Direct bank transfer to our account</p>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-card" onclick="selectPaymentMethod('cash_on_delivery')">
                                    <div class="d-flex align-items-center">
                                        <div class="payment-icon me-3">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="payment-title">Cash on Delivery</div>
                                            <p class="payment-description">Pay when your order arrives</p>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 fade-in-up" style="animation-delay: 0.3s;">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn proceed-btn text-white" id="proceedBtn" disabled>
                                        <i class="fas fa-lock me-2"></i>Proceed to Payment
                                    </button>
                                    <a href="{{ route('order.create') }}" class="btn back-btn">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-5">
                        <div class="order-summary-card card shadow-sm sticky-top fade-in-up order-summary-container" style="top: 20px;">
                            <div class="card-body p-3">
                                <div class="section-title text-start mb-3">
                                    <h5 class="ff-secondary text-primary fw-normal">Order Summary</h5>
                                    <h4 class="mb-2">Your Order</h4>
                                </div>

                                <div class="customer-info">
                                    <h6 class="mb-2 fw-bold">Customer Details</h6>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="info-text">
                                            <div class="info-label">Name</div>
                                            <div class="info-value">{{ $pendingOrder['customer_details']['CustomerName'] }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="info-text">
                                            <div class="info-label">Email</div>
                                            <div class="info-value">{{ $pendingOrder['customer_details']['CustomerEmail'] }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="info-text">
                                            <div class="info-label">Phone</div>
                                            <div class="info-value">{{ $pendingOrder['customer_details']['CustomerContactNumber'] }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="info-text">
                                            <div class="info-label">Address</div>
                                            <div class="info-value">{{ $pendingOrder['customer_details']['CustomerAddressLine1'] }}, {{ $pendingOrder['customer_details']['CustomerCity'] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h6 class="mb-2 fw-bold">Order Items</h6>
                                    @foreach ($pendingOrder['items'] as $item)
                                        <div class="order-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-semibold">{{ $item['product_name'] }}</div>
                                                    <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                                                </div>
                                                <div class="fw-bold" style="color: var(--primary);">PHP {{ number_format($item['line_total'], 2) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="total-section">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h6 mb-1">Total Amount</div>
                                            <small>Including all taxes and fees</small>
                                        </div>
                                        <div class="h4 mb-0">PHP {{ number_format($pendingOrder['total_amount'], 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once resource_path('includes/footer.php'); ?>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <?php include_once resource_path('includes/js_includes.php'); ?>
    <script>
        function selectPaymentMethod(method) {
            // Remove selected class from all cards
            document.querySelectorAll('.payment-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Check the corresponding radio button
            document.getElementById(method).checked = true;
            
            // Enable the proceed button
            document.getElementById('proceedBtn').disabled = false;
        }
        
        // Handle radio button changes
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selected class from all cards
                document.querySelectorAll('.payment-card').forEach(card => {
                    card.classList.remove('selected');
                });
                
                // Add selected class to parent card
                this.closest('.payment-card').classList.add('selected');
                
                // Enable the proceed button
                document.getElementById('proceedBtn').disabled = false;
            });
        });
    </script>
</body>
</html>
