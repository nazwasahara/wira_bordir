@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="auth-card">
                <div class="row g-0">
                    <!-- Sisi Kiri - Gambar -->
                    <div class="col-lg-5">
                        <div class="auth-image-section">
                            <div class="auth-image-content">
                                <img src="{{ asset('/images/ilustrasi-login.png') }}" alt="Ilustrasi Login" class="img-fluid mb-4">
                                <h2>Selamat Datang Kembali!</h2>
                                <p>Masuk untuk mengakses akun Anda dan melanjutkan aktivitas bersama kami.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan - Form -->
                    <div class="col-lg-7">
                        <div class="auth-form-section">
                            <div class="auth-form-wrapper">
                                <div class="auth-header">
                                    <h3>Masuk ke Akun Anda</h3>
                                    <p>Masukkan email dan kata sandi untuk melanjutkan</p>
                                </div>

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Alamat Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Masukkan email Anda"
                                                   required 
                                                   autofocus>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Kata Sandi</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Masukkan kata sandi"
                                                   required>
                                            <button class="btn btn-toggle-password" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4 form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-auth-primary w-100 mb-3 text-white">
                                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                    </button>

                                    <div class="text-center">
                                        <p class="mb-0 text-muted">
                                            Belum punya akun? 
                                            <a href="{{ route('register') }}" class="auth-link">Daftar di sini</a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
@endpush
