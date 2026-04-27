<!-- Sidebar -->
<div class="col-md-3 bg-light p-4" style="min-height: 100vh;">
    <h5 class="mb-4">Menu</h5>
    <ul class="list-unstyled">
        <li class="mb-2">
            <a href="{{ route('dashboard') }}" class="text-decoration-none text-dark {{ request()->routeIs('dashboard') ? 'fw-bold' : '' }}">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
        </li>
        <li class="mb-2">
            <a href="{{ route('about') }}" class="text-decoration-none text-dark {{ request()->routeIs('about') ? 'fw-bold' : '' }}">
                <i class="bi bi-info-circle"></i> About
            </a>
        </li>
        <li class="mb-2">
            <a href="{{ route('profile.show') }}" class="text-decoration-none text-dark {{ request()->routeIs('profile.*') ? 'fw-bold' : '' }}">
                <i class="bi bi-person"></i> Profile
            </a>
        </li>
    </ul>
</div>
