@extends('layouts.admin')

@section('title', 'Tambah Warna Pita Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-ribbon me-2"></i>Tambah Warna Pita Baru</h2>
    <a href="{{ route('services.ribbon-colors.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.ribbon-colors.store') }}" method="POST" id="ribbonColorForm">
            @csrf
            
            <!-- Ribbon Color Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Warna Pita</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">
                                Nama Warna <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Contoh: Merah, Biru, Gold, Silver, dll"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan nama warna pita untuk dekorasi sash</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">
                                Harga <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', 0) }}" 
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Harga tambahan untuk warna ini</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-lightbulb me-2"></i>
                                <div>
                                    <small><strong>Tips:</strong> Gunakan harga 0 untuk warna standar. Warna premium atau warna khusus bisa dikenakan harga tambahan.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Warna Pita
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="{{ route('services.ribbon-colors.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Help Sidebar -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Panduan</h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Tips Menambah Warna Pita:</h6>
                <ul class="small">
                    <li class="mb-2">Pastikan nama warna jelas dan mudah dikenali</li>
                    <li class="mb-2">Gunakan harga 0 untuk warna standar</li>
                    <li class="mb-2">Warna premium/khusus bisa dikenakan harga tambahan</li>
                    <li class="mb-2">Nama warna harus unik dalam sistem</li>
                </ul>
                
                <hr>
                
                <h6 class="fw-bold">Contoh Warna Pita:</h6>
                <div class="bg-light p-3 rounded mb-3">
                    <small>
                        <strong>Warna Standar:</strong><br>
                        - Merah (Rp 0)<br>
                        - Biru (Rp 0)<br>
                        - Putih (Rp 0)<br>
                        - Hitam (Rp 0)
                    </small>
                </div>

                <div class="bg-light p-3 rounded">
                    <small>
                        <strong>Warna Premium:</strong><br>
                        - Gold (Rp 5.000)<br>
                        - Silver (Rp 5.000)<br>
                        - Rose Gold (Rp 7.000)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 100;
}

@media (max-width: 991px) {
    .sticky-top {
        position: relative;
        top: 0;
    }
}

.card {
    transition: all 0.3s ease;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('scripts')
<script>
// Form validation and submit handling
document.getElementById('ribbonColorForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Reset button confirmation
document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
    if (!confirm('Apakah Anda yakin ingin mereset form ini?')) {
        e.preventDefault();
    }
});

// Price validation
const priceInput = document.getElementById('price');
priceInput?.addEventListener('input', function() {
    if (this.value < 0) {
        this.value = 0;
    }
});

// Auto capitalize color name
const nameInput = document.getElementById('name');
nameInput?.addEventListener('blur', function() {
    // Capitalize first letter of each word
    this.value = this.value.split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
});
</script>
@endpush