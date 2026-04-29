<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once resource_path('views/admin/partials/head/head-meta.php'); ?>
  <title>Restawran - Admin Dashboard</title>
   <link rel="stylesheet" href="{{ asset('node_modules/swiper/swiper-bundle.min.css') }}" />
  <?php include_once resource_path('views/admin/partials/head/head-links.php'); ?>
</head>

<body>
  <!-- Vertical Sidebar -->
  <div>
    <?php include_once resource_path('views/admin/partials/sidebar-collapse.php'); ?>

    <!-- Main Content -->
    <div id="content" class="position-relative h-100">
      <?php include_once resource_path("views/admin/partials/topbar-second.php")?>
      <!-- container -->
      <div class="custom-container">
        <!-- row -->
        <div class="row mb-6 g-6">
          <div class="col-xl-8 col-lg-6">
            <div class="bg-gradient-mixed p-8 py-10 rounded-3 p-lg-7">
              <!--heading-->
              <h1 class="fs-3">👋 Hello <?php echo session('user_name')?>,</h1>
              <p class="mb-0">Welcome to your E-commerce Dashboard! Monitor your sales,</p>
              <p>track your progress, and gain valuable insights.</p>
            </div>
          </div>
          <div class="col-xl-4 col-lg-6">
            <!-- card -->
          </div>
        </div>

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

        <!-- row -->
        @yield('content')

      </div>
    </div>
  </div>

  <?php include_once resource_path('views/admin/partials/scripts.html')?>
  <!-- jsvectormap -->
  <script src="{{ asset('assets/js/vendors/sidebarnav.js') }}"></script>
  <script src="{{ asset('node_modules/jsvectormap/dist/js/jsvectormap.min.js') }}"></script>
  <script src="{{ asset('node_modules/jsvectormap/dist/maps/world.js') }}"></script>
  <script src="{{ asset('node_modules/jsvectormap/dist/maps/world-merc.js') }}"></script>
  <script src="{{ asset('node_modules/apexcharts/dist/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/js/vendors/chart.js') }}"></script>
  <script src="{{ asset('node_modules/choices.js/public/assets/scripts/choices.min.js') }}"></script>
  <script src="{{ asset('assets/js/vendors/choice.js') }}"></script>
  <script src="{{ asset('node_modules/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/vendors/swiper.js') }}"></script>
</body>

</html>
