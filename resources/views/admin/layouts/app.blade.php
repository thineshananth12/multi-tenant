<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>

    {{-- Link local Bootstrap CSS --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

  
    <div class="d-flex justify-content-between align-items-center p-3 bg-light shadow-sm">

        <div class="d-flex align-items-center">
            <a class="navbar-brand">@yield('title','My App')</a>
        </div>

        <div class="d-flex gap-3">
            <a role="button" id="loadTenants" class="text-dark text-decoration-none">Tenants</a>
        </div>

        <div class="d-flex align-items-center">
            <span class="me-2 fw-semibold text-dark" id="userName"></span>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('images/profile.png') }}" alt="Profile" class="rounded-circle" width="20" height="20">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" id="logoutButton">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Main content area --}}
    <div class="container">
        @yield('content')
    </div>

    {{-- Bootstrap JS --}}
    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('js/admin.js') }}"></script>

</body>
</html>
