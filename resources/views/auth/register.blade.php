@extends('layouts.auth')

@section('title', 'Daftar')

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
                                <img src="{{ asset('/images/ilustrasi-login.png') }}" alt="Ilustrasi Daftar" class="img-fluid mb-4">
                                <h2>Gabung Sekarang!</h2>
                                <p>Buat akun Anda dan mulai pengalaman berbelanja yang menyenangkan bersama kami.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan - Form -->
                    <div class="col-lg-7">
                        <div class="auth-form-section">
                            <div class="auth-form-wrapper">
                                <div class="auth-header">
                                    <h3>Buat Akun Baru</h3>
                                    <p>Lengkapi data di bawah untuk memulai</p>
                                </div>

                                <form action="{{ route('register') }}" method="POST">
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">
                                                Nama Pengguna <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control @error('username') is-invalid @enderror" 
                                                       id="username" 
                                                       name="username" 
                                                       value="{{ old('username') }}" 
                                                       placeholder="Masukkan nama pengguna"
                                                       required>
                                            </div>
                                            @error('username')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">
                                                Alamat Email <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                                <input type="email" 
                                                       class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" 
                                                       name="email" 
                                                       value="{{ old('email') }}" 
                                                       placeholder="Masukkan email"
                                                       required>
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="phone_number" class="form-label">
                                                Nomor Telepon <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-phone"></i>
                                                </span>
                                                <input type="tel" 
                                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                                       id="phone_number" 
                                                       name="phone_number" 
                                                       value="{{ old('phone_number') }}" 
                                                       placeholder="Contoh: 08123456789"
                                                       required>
                                            </div>
                                            @error('phone_number')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">
                                            Alamat <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" 
                                                      name="address" 
                                                      rows="2" 
                                                      placeholder="Masukkan alamat lengkap"
                                                      required>{{ old('address') }}</textarea>
                                        </div>
                                        @error('address')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">
                                                Kata Sandi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password" 
                                                       placeholder="Minimal 8 karakter"
                                                       required>
                                                <button class="btn btn-toggle-password" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label">
                                                Konfirmasi Kata Sandi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="password_confirmation" 
                                                       name="password_confirmation" 
                                                       placeholder="Ulangi kata sandi"
                                                       required>
                                                <button class="btn btn-toggle-password" type="button" id="togglePasswordConfirm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-auth-primary w-100 mb-3 text-white">
                                        <i class="fas fa-user-plus me-2"></i>Buat Akun
                                    </button>

                                    <div class="text-center">
                                        <p class="mb-0 text-muted">
                                            Sudah punya akun? 
                                            <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a>
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

    document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
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
