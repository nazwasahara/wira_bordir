<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
        <button class="btn btn-link text-dark" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Quick Stats -->
            <div class="d-none d-lg-flex me-4 quick-stats">
                <div class="px-3 border-end">
                    <small class="text-muted d-block">Penjualan Hari Ini</small>
                    <strong class="text-success">
                        Rp {{ number_format($navbar_today_sales ??  0, 0, ',', '.') }}
                    </strong>
                </div>
                <div class="px-3">
                    <small class="text-muted d-block">Pesanan Pending</small>
                    <strong class="text-warning">
                        {{ number_format($navbar_pending_orders ?? 0) }}
                    </strong>
                </div>
            </div>
            
            <!-- User Profile -->
            <div class="dropdown">
                <button class="btn btn-link text-dark text-decoration-none d-flex align-items-center" 
                        type="button" 
                        id="userDropdown" 
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <div class="avatar me-2">
                        <i class="fas fa-user-circle fa-2x text-success"></i>
                    </div>
                    <div class="text-start d-none d-md-block">
                        <div class="fw-bold">{{ Auth::user()->username }}</div>
                        <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                    <li>
                        <div class="dropdown-header">
                            <strong>{{ Auth::user()->username }}</strong><br>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="{{ route('owner.dashboard') }}" class="dropdown-item">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>