<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/logo.png') }}" 
                 alt="Logo" 
                 class="sidebar-logo me-3">
            <h4 class="mb-0">
               Panel Admin
            </h4>
        </div>
    </div>
    
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-header">
            <span>MANAJEMEN UTAMA</span>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Pesanan</span>
            </a>
        </li>
        
        <li class="nav-header">
            <span>CUSTOMISASI SELEMPANG</span>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.materials.index') }}" class="nav-link {{ request()->routeIs('services.materials.*') ? 'active' : '' }}">
                <i class="fas fa-palette"></i>
                <span>Material & Warna</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.fonts.index') }}" class="nav-link {{ request()->routeIs('services.fonts.*') ? 'active' : '' }}">
                <i class="fas fa-font"></i>
                <span>Font</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.ribbon-colors.index') }}" class="nav-link {{ request()->routeIs('services.ribbon-colors.*') ? 'active' : '' }}">
                <i class="fas fa-ribbon"></i>
                <span>Warna Pita</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.side-motifs.index') }}" class="nav-link {{ request()->routeIs('services.side-motifs.*') ? 'active' : '' }}">
                <i class="fas fa-shapes"></i>
                <span>Motif Samping</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.sash-types.index') }}" class="nav-link {{ request()->routeIs('services.sash-types.*') ? 'active' : '' }}">
                <i class="fas fa-tshirt"></i>
                <span>Jenis Selempang</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.lace-options.index') }}" class="nav-link {{ request()->routeIs('services.lace-options.*') ? 'active' : '' }}">
                <i class="fas fa-cut"></i>
                <span>Opsi Renda</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.rombe-options.index') }}" class="nav-link {{ request()->routeIs('services.rombe-options.*') ? 'active' : '' }}">
                <i class="fas fa-gem"></i>
                <span>Opsi Rombe</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.motif-ribbon-options.index') }}" class="nav-link {{ request()->routeIs('services.motif-ribbon-options.*') ? 'active' : '' }}">
                <i class="fas fa-wind"></i>
                <span>Pita Motif</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('services.additional-items.index') }}" class="nav-link {{ request()->routeIs('services.additional-items.*') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i>
                <span>Item Tambahan</span>
            </a>
        </li>
        
        <li class="nav-header">
            <span>KONTEN</span>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.galleries.index') }}" class="nav-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                <i class="fas fa-images"></i>
                <span>Galeri</span>
            </a>
        </li>
        
    </ul>
</aside>