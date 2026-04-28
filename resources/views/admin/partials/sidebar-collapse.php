<div id="miniSidebar">
  <div class="brand-logo">
    <a class="d-none d-md-flex align-items-center gap-2" href="/public/admin/index">
      <i class="fa fa-utensils me-3"></i>
      <span class="fw-bold fs-4 site-logo-text">Restawran</span>
    </a>
  </div>

  <ul class="navbar-nav flex-column">

    <!-- HOME LINK -->
    <li class="nav-item">
      <a class="nav-link" href="/public/" target="_blank">
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
    <li class="nav-item">
      <a class="nav-link" href="/public/admin/lookup">
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

    <!-- PRODUCT -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l8 4.5v9l-8 4.5l-8-4.5v-9z"/>
          </svg>
        </span>
        <span class="text">Product</span>
      </a>

      <ul class="dropdown-menu flex-column">
        <li><a class="nav-link" href="/public/admin/product">Products</a></li>
        <li><a class="nav-link" href="/public/admin/productinventory">Product Inventory</a></li>
      </ul>
    </li>

    <!-- CUSTOMERS -->
    <li class="nav-item">
      <a class="nav-link" href="/public/admin/customers">
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

    <!-- USERS -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="7" r="4"/>
            <path d="M5.5 21h13a2 2 0 0 0 2-2v-2a7 7 0 0 0-14 0v2a2 2 0 0 0 2 2z"/>
          </svg>
        </span>
        <span class="text">Users</span>
      </a>

      <ul class="dropdown-menu flex-column">
        <li><a class="nav-link" href="/public/admin/users">Users</a></li>
        <li><a class="nav-link" href="/public/admin/userprofile">User Profiles</a></li>
        <li><a class="nav-link" href="/public/admin/userprofprivileges">Privileges</a></li>
      </ul>
    </li>

    <!-- ORDERS -->
    <li class="nav-item">
      <a class="nav-link" href="/public/admin/orders">
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

    <!-- PAYMENTS -->
    <li class="nav-item">
      <a class="nav-link" href="/public/admin/payments">
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

    <!-- REPORTS -->
    <li class="nav-item">
      <a class="nav-link" href="/public/admin/reports">
        <span class="nav-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
               viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 12h18"/>
            <path d="M7 6v12"/>
            <path d="M12 4v16"/>
            <path d="M17 8v8"/>
          </svg>
        </span>
        <span class="text">Reports</span>
      </a>
    </li>

  </ul>
</div>