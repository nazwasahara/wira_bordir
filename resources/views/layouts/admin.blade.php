<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Panel Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    @if(auth()->user()->role === 'admin')
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
     @elseif(auth()->user()->role === 'owner')
        <link href="{{ asset('css/owner.css') }}" rel="stylesheet">
    @endif
    @stack('styles')
</head>
<body class="admin-dashboard">
    <div class="wrapper">
            @if(auth()->user()->role === 'admin')
            @include('partials.admin.sidebar')
            @elseif(auth()->user()->role === 'owner')
                @include('partials.owner.sidebar')
            @endif
        
        <div class="main-content" id="mainContent">
            @if(auth()->user()->role === 'admin')
            @include('partials.admin.navbar')
            @elseif(auth()->user()->role === 'owner')
                @include('partials.owner.navbar')
            @endif
            
            <main class="content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
            
            @if(auth()->user()->role === 'admin')
            @include('partials.admin.footer')
            @elseif(auth()->user()->role === 'owner')
                @include('partials.owner.footer')
            @endif
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom Admin JS -->
<script>
    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (window.innerWidth <= 991) {
            // Mobile behavior
            sidebar.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        } else {
            // Desktop behavior
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Force reflow untuk fix layout
            void mainContent.offsetWidth;
        }
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 991) {
            const isClickInside = sidebar.contains(event.target) || 
                                 sidebarToggle.contains(event.target);
            
            if (!isClickInside && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 991) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
            
            // Force reflow setelah resize
            void mainContent.offsetWidth;
        }, 250);
    });
    
    // Close sidebar when clicking nav link on mobile
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        });
    });
    
    // Fix layout on page load
    window.addEventListener('load', function() {
        if (window.innerWidth > 991) {
            if (sidebar.classList.contains('collapsed')) {
                mainContent.classList.add('expanded');
            }
        }
    });
</script>
@stack('scripts')
</body>
</html>