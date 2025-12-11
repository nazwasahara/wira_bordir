@extends('layouts.admin')

@section('title', 'Edit Warna Pita')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Warna Pita</h2>
    <div>
        <a href="{{ route('services.ribbon-colors.show', $ribbonColor) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('services.ribbon-colors.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.ribbon-colors.update', $ribbonColor) }}" method="POST" id="ribbonColorForm">
            @csrf
            @method('PUT')
            
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
                                   value="{{ old('name', $ribbonColor->name) }}" 
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
                                       value="{{ old('price', $ribbonColor->price) }}" 
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

                        @if($ribbonColor->isUsedInOrders())
                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <small><strong>Perhatian!</strong> Warna ini sudah digunakan dalam {{ $ribbonColor->orderItems()->distinct('order_id')->count('order_id') }} pesanan. Perubahan harga tidak akan mempengaruhi pesanan yang sudah ada.</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <small class="text-muted d-block">Dibuat Pada</small>
                            <strong>{{ $ribbonColor->created_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $ribbonColor->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Terakhir Diperbarui</small>
                            <strong>{{ $ribbonColor->updated_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $ribbonColor->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <a href="{{ route('services.ribbon-colors.show', $ribbonColor) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                        <a href="{{ route('services.ribbon-colors.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Warna</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga Saat Ini:</span>
                        <strong class="text-success">{{ $ribbonColor->formatted_price }}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tipe:</span>
                        @if($ribbonColor->price == 0)
                            <span class="badge bg-info">Standar</span>
                        @else
                            <span class="badge bg-primary">Premium</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Digunakan:</span>
                        @if($ribbonColor->isUsedInOrders())
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Ya
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Belum
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Pesanan:</span>
                        <strong>{{ $ribbonColor->orderItems()->distinct('order_id')->count('order_id') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Preview -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Preview Warna</h6>
            </div>
            <div class="card-body text-center">
                <div class="color-preview-large mx-auto" style="background: {{ getColorCode($ribbonColor->name) }}"></div>
                <p class="mt-3 mb-0 fw-bold">{{ $ribbonColor->name }}</p>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-3">Panduan Edit Warna:</h6>
                <ul class="small mb-3">
                    <li class="mb-2">Pastikan nama warna tetap jelas dan unik</li>
                    <li class="mb-2">Perubahan harga tidak mempengaruhi pesanan lama</li>
                    <li class="mb-2">Warna yang sudah digunakan tidak bisa dihapus</li>
                    <li class="mb-2">Simpan perubahan sebelum meninggalkan halaman</li>
                </ul>

                <div class="alert alert-info small mb-0" role="alert">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Info:</strong> Perubahan akan langsung berlaku untuk pesanan baru.
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

.color-preview-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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
    if (!confirm('Apakah Anda yakin ingin mereset form ini? Semua perubahan yang belum disimpan akan hilang.')) {
        e.preventDefault();
    }
});

// Warn before leaving with unsaved changes
let formChanged = false;

document.getElementById('ribbonColorForm')?.addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

document.getElementById('ribbonColorForm')?.addEventListener('submit', function() {
    formChanged = false;
});

// Price validation
const priceInput = document.getElementById('price');
priceInput?.addEventListener('input', function() {
    if (this.value < 0) {
        this.value = 0;
    }
});
</script>
@endpush

@php
// Helper function untuk mapping warna
function getColorCode($colorName) {
    $colors = [
        'Merah' => '#DC143C',
        'Biru' => '#1E90FF',
        'Hijau' => '#32CD32',
        'Kuning' => '#FFD700',
        'Hitam' => '#000000',
        'Putih' => '#FFFFFF',
        'Pink' => '#FF69B4',
        'Ungu' => '#9370DB',
        'Orange' => '#FF8C00',
        'Coklat' => '#8B4513',
        'Abu-abu' => '#808080',
        'Gold' => '#FFD700',
        'Silver' => '#C0C0C0',
        'Navy' => '#000080',
        'Tosca' => '#40E0D0',
        'Maroon' => '#800000',
        'Cream' => '#FFFDD0',
        'Rose Gold' => '#B76E79',
    ];
    
    return $colors[$colorName] ?? '#999999';
}
@endphp