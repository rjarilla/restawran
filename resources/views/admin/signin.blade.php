<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once resource_path('views/admin/partials/head/head-meta.php'); ?>
    <title>Sign In | Restawran - Admin Dashboard</title>
    <?php include_once resource_path('views/admin/partials/head/head-links.php'); ?>
  </head>

  <body>
    <main class="d-flex flex-column justify-content-center vh-100">
      <!--Sign up start-->
      <section>
        <div class="container">
          <div class="row mb-8">
            <div class="col-xl-4 offset-xl-4 col-md-12 col-12">
              <div class="text-center" style="color:#FEA116 !important">
                <a href="#" class="fs-2 fw-bold d-flex align-items-center gap-2 justify-content-center mb-6" style="color:#FEA116 !important">
                  <i class="fa fa-utensils me-3"></i><span>Restawran</span>
                </a>
                <h1 class="mb-1">Login</h1>
                
              </div>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-12">
              <div class="card card-lg mb-6">
                <div class="card-body p-6">
                  @php
                    // Redirect if already logged in
                    if(session('user_id')) {
                      header('Location: ' . url('admin/index'));
                      exit;
                    }
                    // Get remembered username
                    $remembered = isset($_COOKIE['remembered_username']) ? $_COOKIE['remembered_username'] : '';
                  @endphp
                  @if(session('login_error'))
                    <div class="alert alert-danger">{{ session('login_error') }}</div>
                  @endif
                  <form class="needs-validation mb-6" method="POST" action="{{ url('admin/login') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                      <label for="signinUsernameInput" class="form-label">
                        User Name <span class="text-danger">*</span>
                      </label>
                      <input type="text" name="username" class="form-control" id="signinUsernameInput" value="{{ old('username', $remembered) }}" required />
                      <div class="invalid-feedback">Please enter user name.</div>
                    </div>
                    <div class="mb-3">
                      <label for="formSignUpPassword" class="form-label">Password <span class="text-danger">*</span></label>
                      <div class="password-field position-relative">
                        <input type="password" name="password" class="form-control fakePassword" id="formSignUpPassword" required />
                        <span><i class="ti ti-eye-off passwordToggler"></i></span>
                        <div class="invalid-feedback">Please enter password.</div>
                      </div>
                    </div>
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMeCheckbox" {{ old('remember') ? 'checked' : '' }} />
                        <label class="form-check-label" for="rememberMeCheckbox">Remember me</label>
                      </div>
                      <div><a href="forget-password.html" class="text-primary">Forgot Password</a></div>
                    </div>
                    <div class="d-grid">
                      <button class="btn btn-primary" type="submit">Sign In</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!--Sign up end-->
      <div class="position-absolute end-0 bottom-0 m-4">
        <div class="dropdown">
          <button class="btn btn-light btn-icon rounded-circle d-flex align-items-center" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)">
            <i class="bi theme-icon-active lh-1"><i class="bi theme-icon bi-sun-fill"></i></i>
            <span class="visually-hidden bs-theme-text">Toggle theme</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
              <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="light" aria-pressed="true">
                <i class="ti theme-icon ti ti-sun"></i>
                <span class="ms-2">Light</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                <i class="ti theme-icon ti-moon-stars"></i>
                <span class="ms-2">Dark</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                <i class="ti theme-icon ti-circle-half-2"></i>
                <span class="ms-2">Auto</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </main>

    <?php include_once resource_path('views/admin/partials/scripts.html'); ?>  
    <script src="/public/assets/js/vendors/password.js"></script>
  </body>
</html>
