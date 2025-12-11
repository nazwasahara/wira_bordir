@extends('layouts.admin')

@section('title', 'Edit Jenis Selempang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Jenis Selempang</h2>
    <div>
        <a href="{{ route('services.sash-types.show', $sashType) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('services.sash-types.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.sash-types.update', $sashType) }}" method="POST" id="sashTypeForm">
            @csrf
            @method('PUT')
            
            <!-- Sash Type Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jenis Selempang</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">
                                Nama Tipe <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $sashType->name) }}" 
                                   placeholder="Contoh: Model Standar, Model Premium, Model Ekslusif, dll"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan nama tipe/model sash</small>
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
                                       value="{{ old('price', $sashType->price) }}" 
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Harga dasar untuk tipe ini</small>
                        </div>

                        @if($sashType->isUsedInOrders())
                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <small><strong>Perhatian!</strong> Tipe ini sudah digunakan dalam {{ $sashType->orderItems()->distinct('order_id')->count('order_id') }} pesanan. Perubahan harga tidak akan mempengaruhi pesanan yang sudah ada.</small>
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
                            <strong>{{ $sashType->created_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $sashType->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Terakhir Diperbarui</small>
                            <strong>{{ $sashType->updated_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $sashType->updated_at->diffForHumans() }}</small>
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
                        <a href="{{ route('services.sash-types.show', $sashType) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                        <a href="{{ route('services.sash-types.index') }}" class="btn btn-outline-danger">
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
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Tipe</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga Saat Ini:</span>
                        <strong class="text-success">{{ $sashType->formatted_price }}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Pesanan:</span>
                        <strong>{{ $sashType->orderItems()->distinct('order_id')->count('order_id') }}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Digunakan:</span>
                        @if($sashType->isUsedInOrders())
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
                        <span class="text-muted">Unit Terjual:</span>
                        <strong>{{ $sashType->orderItems()->sum('quantity') }} Unit</strong>
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
                <h6 class="fw-bold mb-3">Panduan Edit Tipe:</h6>
                <ul class="small mb-3">
                    <li class="mb-2">Pastikan nama tipe tetap jelas dan unik</li>
                    <li class="mb-2">Perubahan harga tidak mempengaruhi pesanan lama</li>
                    <li class="mb-2">Tipe yang sudah digunakan tidak bisa dihapus</li>
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

@media (max-width: 991px) {
    .sticky-top {
        position: relative;
        top: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('sashTypeForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
    if (!confirm('Apakah Anda yakin ingin mereset form ini? Semua perubahan yang belum disimpan akan hilang.')) {
        e.preventDefault();
    }
});

let formChanged = false;

document.getElementById('sashTypeForm')?.addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

document.getElementById('sashTypeForm')?.addEventListener('submit', function() {
    formChanged = false;
});

const priceInput = document.getElementById('price');
priceInput?.addEventListener('input', function() {
    if (this.value < 0) {
        this.value = 0;
    }
});
</script>
@endpush