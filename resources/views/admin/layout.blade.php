<!DOCTYPE html>
<html>
<head>
    <title>RMS Admin</title>

    <!-- Bootstrap (optional but recommended) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark px-3">
        <span class="navbar-brand">RMS Admin Dashboard</span>
        <a href="/admin/customers" class="text-white">Customers</a>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

</body>
</html>