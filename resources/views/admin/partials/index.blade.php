<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once resource_path('views/admin/partials/head/head-meta.php'); ?>
  <title>Restawran - Admin Dashboard</title>
   <link rel="stylesheet" href="/node_modules/swiper/swiper-bundle.min.css" />
  <?php include_once resource_path('views/admin/partials/head/head-links.php'); ?>
</head>

<body>
  <!-- Vertical Sidebar -->
  <div>
    @include('admin.partials.sidebar-collapse')

    <!-- Main Content -->
    <div id="content" class="position-relative h-100">
      @include('admin.partials.topbar-second')
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
        <!-- row -->
        @yield('content')

      </div>
    </div>
  </div>

  <?php include_once resource_path('views/admin/partials/scripts.html')?>
  <!-- jsvectormap -->
  <script src="/assets/js/vendors/sidebarnav.js"></script>
  <script src="/node_modules/jsvectormap/dist/js/jsvectormap.min.js"></script>
  <script src="/node_modules/jsvectormap/dist/maps/world.js"></script>
  <script src="/node_modules/jsvectormap/dist/maps/world-merc.js"></script>
  <script src="/node_modules/apexcharts/dist/apexcharts.min.js"></script>
  <script src="/assets/js/vendors/chart.js"></script>
  <script src="/node_modules/choices.js/assets/scripts/choices.min.js"></script>
  <script src="/assets/js/vendors/choice.js"></script>
  <script src="/node_modules/swiper/swiper-bundle.min.js"></script>
  <script src="/assets/js/vendors/swiper.js"></script>
</body>

</html>
