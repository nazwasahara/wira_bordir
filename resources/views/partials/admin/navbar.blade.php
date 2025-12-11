<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
        <button class="btn btn-link text-dark" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Profil Pengguna -->
            <div class="dropdown">
                <button class="btn btn-link text-dark text-decoration-none d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                    <div class="avatar me-2">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <div class="text-start d-none d-md-block">
                        <div class="fw-bold">{{ Auth::user()->username }}</div>
                        <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
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