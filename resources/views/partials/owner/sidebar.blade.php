<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/logo.png') }}" 
                 alt="Logo" 
                 class="sidebar-logo me-3">
            <h4 class="mb-0">
                Panel Pemilik
            </h4>
        </div>
    </div>
    
    <ul class="sidebar-nav">
        <!-- Dashboard -->
        <li class="nav-item">
            <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-header">
            <span>ANALITIK & LAPORAN</span>
        </li>
        
        <!-- Requirement #2: Ringkasan Pesanan -->
        <li class="nav-item">
            <a href="{{ route('owner.orders.summary') }}" class="nav-link {{ request()->routeIs('owner.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Ringkasan Pesanan</span>
            </a>
        </li>
        
        <!-- Requirement #5: Grafik Penjualan -->
        <li class="nav-item">
            <a href="{{ route('owner.sales.analytics') }}" class="nav-link {{ request()->routeIs('owner.sales.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Grafik Penjualan</span>
            </a>
        </li>
        
        <!-- Requirement #6: Laporan Keuangan -->
        <li class="nav-item">
            <a href="{{ route('owner.reports.financial') }}" class="nav-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Laporan Keuangan</span>
            </a>
        </li>
        
        <!-- Requirement #7: Produk Terlaris -->
        <li class="nav-item">
            <a href="{{ route('owner.products.bestseller') }}" class="nav-link {{ request()->routeIs('owner.products.bestseller') ? 'active' : '' }}">
                <i class="fas fa-trophy"></i>
                <span>Produk Terlaris</span>
            </a>
        </li>
        
        <!-- Requirement #8: Riwayat Transaksi -->
        <li class="nav-item">
            <a href="{{ route('owner.transactions.history') }}" class="nav-link {{ request()->routeIs('owner.transactions.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Riwayat Transaksi</span>
            </a>
        </li>

        <li class="nav-header">
            <span>INVENTORI</span>
        </li>

        <li class="nav-item">
            <a href="{{ route('owner.purchase-invoices.index') }}" 
            class="nav-link {{ request()->routeIs('owner.purchase-invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i>
                <span>Invoice Pembelian</span>
            </a>
        </li>
        
        <li class="nav-header">
            <span>MANAJEMEN</span>
        </li>
        
        <!-- Requirement #3: Manajemen Pengguna -->
        <li class="nav-item">
            <a href="{{ route('owner.users.index') }}" class="nav-link {{ request()->routeIs('owner.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Manajemen Pengguna</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('owner.logs.admin-activity.index') }}" class="nav-link {{ request()->routeIs('owner.logs.admin-activity.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i>
                <span>Log Aktivitas Admin</span>
            </a>
        </li>
        

        <li class="nav-header">
            <span>LAYANAN BORDIR</span>
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
        
        {{-- <li class="nav-header">
            <span>LAINNYA</span>
        </li> --}}
        
        <!-- Gallery (View Only) -->
        {{-- <li class="nav-item">
            <a href="#" class="nav-link {{ request()->routeIs('owner.galleries.*') ? 'active' : '' }}">
                <i class="fas fa-images"></i>
                <span>Galeri</span>
            </a>
        </li> --}}
        
    </ul>
</aside>