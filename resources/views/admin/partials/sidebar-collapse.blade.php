<div id="miniSidebar">
  <div class="brand-logo">
    <a class="d-none d-md-flex align-items-center gap-2" href="{{ route('admin.index') }}">
      <i class="fa fa-utensils me-3"></i>
      <span class="fw-bold fs-4 site-logo-text">Restawran</span>
    </a>
  </div>

  <ul class="navbar-nav flex-column">

    <!-- HOME LINK -->
    <li class="nav-item">
      <a class="nav-link" href="/" target="_blank">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6.331 8h11.339a2 2 0 0 1 1.977 2.304l-1.255 8.152a3 3 0 0 1-2.966 2.544h-6.852a3 3 0 0 1-2.965-2.544l-1.255-8.152a2 2 0 0 1 1.977-2.304z"/>
            <path d="M9 11v-5a3 3 0 0 1 6 0v5"/>
          </svg>
        </span>
        <span class="text">Restawran.com</span>
      </a>
    </li>

    <li class="nav-item">
      <div class="nav-heading">MODULES</div>
      <hr class="mx-5 nav-line mb-1" />
    </li>

    <!-- LOOKUP -->
    @if(in_array('LOOKUP', $privs))
    <li class="nav-item">
      <a class="nav-link" href="{{ route('admin.lookup.index') }}">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 6h11"/>
            <path d="M9 12h11"/>
            <path d="M9 18h11"/>
            <path d="M5 6v.01"/>
            <path d="M5 12v.01"/>
            <path d="M5 18v.01"/>
          </svg>
        </span>
        <span class="text">Lookup</span>
      </a>
    </li>
    @endif

    <!-- PRODUCT -->
    @if(in_array('PROD', $privs) || in_array('PROD_INV', $privs))
    <li class="nav-item">
      <button class="nav-link collapsed w-100 text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#productSubmenu" aria-expanded="false" aria-controls="productSubmenu">
        <span class="d-flex align-items-center gap-2">
          <span class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3l8 4.5v9l-8 4.5l-8-4.5v-9z"/>
            </svg>
          </span>
          <span class="text">Product</span>
        </span>
        <span class="nav-caret">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9" />
          </svg>
        </span>
      </button>
      <div class="collapse" id="productSubmenu">
        <ul class="list-unstyled ms-4">
          <li class="nav-item">

            <a class="nav-link" href="{{ route('admin.product.index') }}">
              <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M3 7.5l9 5l9-5" />
                  <path d="M3 17.5l9 5l9-5" />
                  <path d="M3 7.5v10l9 5v-10" />
                  <path d="M21 7.5v10l-9 5v-10" />
                  <path d="M3 7.5l9-5l9 5" />
                </svg>
              </span>
              <span class="text">Products</span>
            </a>
          </li>
          @if(in_array('PROD', $privs) || in_array('PROD_INV', $privs))
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.productinventory.index') }}">
              <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <ellipse cx="12" cy="6" rx="8" ry="3" />
                  <path d="M4 6v6a8 3 0 0 0 16 0V6" />
                  <path d="M4 12v6a8 3 0 0 0 16 0v-6" />
                </svg>
              </span>
              <span class="text">Product Inventory</span>
            </a>
          </li>
          @endif
        </ul>
      </div>
    </li>
    @endif

    <!-- CUSTOMERS -->
    @if(in_array('CUSTOMER', $privs))
    <li class="nav-item">
      <a class="nav-link" href="{{ route('admin.customers.index') }}">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="7" r="4"></circle>
            <path d="M5.5 21h13a2 2 0 0 0 2-2v-1a7 7 0 0 0-14 0v1a2 2 0 0 0 2 2z"></path>
          </svg>
        </span>
        <span class="text">Customers</span>
      </a>
    </li>
    @endif

    <!-- USERS -->
    @if(in_array('USER', $privs))
    <li class="nav-item">
      <button class="nav-link collapsed w-100 text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#userSubmenu" aria-expanded="false" aria-controls="userSubmenu">
        <span class="d-flex align-items-center gap-2">
          <span class="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="7" r="4"/>
              <path d="M5.5 21h13a2 2 0 0 0 2-2v-2a7 7 0 0 0-14 0v2a2 2 0 0 0 2 2z"/>
            </svg>
          </span>
          <span class="text">Users</span>
        </span>
        <span class="nav-caret">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9" />
          </svg>
        </span>
      </button>
      <div class="collapse" id="userSubmenu">
        <ul class="list-unstyled ms-4">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
              <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <circle cx="12" cy="7" r="4" />
                  <path d="M5.5 21h13a2 2 0 0 0 2-2v-2a7 7 0 0 0-14 0v2a2 2 0 0 0 2 2z" />
                </svg>
              </span>
              <span class="text">Users</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.userprofile.index') }}">
              <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <rect x="3" y="4" width="18" height="16" rx="3" />
                  <circle cx="9" cy="10" r="2" />
                  <line x1="15" y1="8" x2="17" y2="8" />
                  <line x1="15" y1="12" x2="17" y2="12" />
                  <line x1="7" y1="16" x2="17" y2="16" />
                </svg>
              </span>
              <span class="text">User Profiles</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.userprofprivileges.index') }}">
              <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <circle cx="8" cy="15" r="4" />
                  <path d="M10.85 12.15l6.65-6.65a2.121 2.121 0 1 1 3 3l-6.65 6.65" />
                  <path d="M15 6l3 3" />
                </svg>
              </span>
              <span class="text">Privileges</span>
            </a>
          </li>
        </ul>
      </div>
    </li>
    @endif

    <!-- ORDERS -->
    @if(in_array('ORDER', $privs))
    <li class="nav-item">
      <a class="nav-link" href="{{ route('admin.orders.index') }}">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4v16a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4"/>
            <path d="M4 8h16"/>
            <path d="M8 12h8"/>
            <path d="M8 16h8"/>
          </svg>
        </span>
        <span class="text">Orders</span>
      </a>
    </li>
    @endif

    <!-- PAYMENTS -->
    @if(in_array('PAYMENT', $privs))
    <li class="nav-item">
      <a class="nav-link" href="{{ route('admin.payments.index') }}">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="5" width="18" height="14" rx="3"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <line x1="7" y1="15" x2="7.01" y2="15"/>
            <line x1="11" y1="15" x2="13" y2="15"/>
          </svg>
        </span>
        <span class="text">Payments</span>
      </a>
    </li>
    @endif

    <!-- REPORTS -->
    @if(in_array('REPORTS', $privs))
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-bar">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M3 12m0 2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z" />
            <path d="M15 10m0 2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2z" />
            <path d="M9 6m0 2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2z" />
            <path d="M3 20h18" />
          </svg>
        </span>
        <span class="text">Reports</span>
      </a>
      <ul class="dropdown-menu flex-column">
        <li class="nav-item">
          <a class="nav-link" href="/admin/reports">
            <span class="nav-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chart-line">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M3 3v18h18" />
                <path d="M20 18l-6 -6l-4 4l-6 -6" />
              </svg>
            </span>
            <span class="text">All Reports</span>
          </a>
        </li>
      </ul>
    </li>
    @endif

  </ul>
</div>
