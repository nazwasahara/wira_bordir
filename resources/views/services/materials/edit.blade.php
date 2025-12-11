@extends('layouts.admin')

@section('title', 'Edit Material')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Material</h2>
    <div>
        <a href="{{ route('services.materials.show', $material) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('services.materials.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.materials.update', $material) }}" method="POST" id="materialForm">
            @csrf
            @method('PUT')
            
            <!-- Material Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Material</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">
                                Nama Material <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $material->name) }}" 
                                   placeholder="Contoh: Satin, Organza, dll"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan nama jenis material</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">
                                Harga Dasar <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $material->price) }}" 
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Harga dasar material</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <small><strong>Catatan:</strong> Untuk mengelola warna material, silakan simpan perubahan ini terlebih dahulu, kemudian kelola warna di halaman detail material.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material Colors Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-swatchbook me-2"></i>Warna Material ({{ $material->colors_count }})</h5>
                    <a href="{{ route('services.materials.show', $material) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-cog me-1"></i>Kelola Warna
                    </a>
                </div>
                <div class="card-body">
                    @if($material->colors->count() > 0)
                    <div class="row g-2">
                        @foreach($material->colors as $color)
                        <div class="col-md-6">
                            <div class="color-preview-card">
                                <div class="d-flex align-items-center">
                                    <div class="color-dot me-2"></div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $color->name }}</strong>
                                        <small class="d-block text-muted">{{ $color->formatted_price }}</small>
                                    </div>
                                    <span class="badge bg-success">{{ $color->formatted_total_price }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Untuk menambah, mengubah, atau menghapus warna, kunjungi halaman detail material setelah menyimpan perubahan.
                        </small>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-palette fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-2">Belum ada warna untuk material ini</p>
                        <a href="{{ route('services.materials.show', $material) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i>Tambah Warna
                        </a>
                    </div>
                    @endif
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
                            <strong>{{ $material->created_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $material->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Terakhir Diperbarui</small>
                            <strong>{{ $material->updated_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $material->updated_at->diffForHumans() }}</small>
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
                        <a href="{{ route('services.materials.show', $material) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                        <a href="{{ route('services.materials.index') }}" class="btn btn-outline-danger">
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
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Material</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Jumlah Warna:</span>
                        <span class="badge bg-info">{{ $material->colors_count }}</span>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga Dasar:</span>
                        <strong class="text-success">{{ $material->formatted_price }}</strong>
                    </div>
                </div>
                @if($material->colors->count() > 0)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga Terendah:</span>
                        <strong>{{ $material->colors->min('price') > 0 ? 'Rp ' . number_format($material->price + $material->colors->min('price'), 0, ',', '.') : $material->formatted_price }}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga Tertinggi:</span>
                        <strong>Rp {{ number_format($material->price + $material->colors->max('price'), 0, ',', '.') }}</strong>
                    </div>
                </div>
                @endif
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Status:</span>
                        @if($material->isUsedInOrders())
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Digunakan
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i>Belum Digunakan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-3">Panduan Edit Material:</h6>
                <ul class="small mb-3">
                    <li class="mb-2">Pastikan nama material tetap unik</li>
                    <li class="mb-2">Perubahan harga akan mempengaruhi perhitungan total harga dengan warna</li>
                    <li class="mb-2">Kelola warna di halaman detail material</li>
                    <li class="mb-2">Simpan perubahan sebelum kelola warna</li>
                </ul>

                @if($material->isUsedInOrders())
                <div class="alert alert-warning small mb-0" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Perhatian!</strong> Material ini sudah digunakan dalam pesanan. Perubahan harga tidak akan mempengaruhi pesanan yang sudah ada.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.color-preview-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.color-preview-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}

.color-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex-shrink: 0;
}

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
document.getElementById('materialForm')?.addEventListener('submit', function(e) {
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

document.getElementById('materialForm')?.addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

document.getElementById('materialForm')?.addEventListener('submit', function() {
    formChanged = false;
});

// Price formatting
const priceInput = document.getElementById('price');
priceInput?.addEventListener('input', function() {
    if (this.value < 0) {
        this.value = 0;
    }
});

</script>
@endpush