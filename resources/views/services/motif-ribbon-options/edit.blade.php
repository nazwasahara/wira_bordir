@extends('layouts.admin')

@section('title', 'Edit Opsi Pita Motif')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Opsi Pita Motif</h2>
    <div>
        <a href="{{ route('services.motif-ribbon-options.show', $motifRibbonOption) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('services.motif-ribbon-options.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.motif-ribbon-options.update', $motifRibbonOption) }}" method="POST" id="motifRibbonOptionForm">
            @csrf
            @method('PUT')
            
            <!-- Motif Ribbon Option Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Opsi Pita Motif</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label">
                                Warna <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('color') is-invalid @enderror" 
                                   id="color" 
                                   name="color" 
                                   value="{{ old('color', $motifRibbonOption->color) }}" 
                                   placeholder="Contoh: Merah Motif Bunga, Biru Polkadot, dll"
                                   required>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan warna dan jenis motif pita</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="size" class="form-label">
                                Ukuran <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('size') is-invalid @enderror" 
                                    id="size" 
                                    name="size" 
                                    required>
                                <option value="">Pilih Ukuran</option>
                                @foreach($sizes as $key => $value)
                                    <option value="{{ $key }}" {{ old('size', $motifRibbonOption->size) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih lebar pita</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="price" class="form-label">
                                Harga <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $motifRibbonOption->price) }}" 
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Harga pita motif</small>
                        </div>

                        @if($motifRibbonOption->isUsedInOrders())
                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <small><strong>Perhatian!</strong> Opsi ini sudah digunakan dalam {{ $motifRibbonOption->orderItems()->distinct('order_id')->count('order_id') }} pesanan. Perubahan harga tidak akan mempengaruhi pesanan yang sudah ada.</small>
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
                            <strong>{{ $motifRibbonOption->created_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $motifRibbonOption->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Terakhir Diperbarui</small>
                            <strong>{{ $motifRibbonOption->updated_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $motifRibbonOption->updated_at->diffForHumans() }}</small>
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
                        <a href="{{ route('services.motif-ribbon-options.show', $motifRibbonOption) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                        <a href="{{ route('services.motif-ribbon-options.index') }}" class="btn btn-outline-danger">
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
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Opsi</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Warna/Motif:</span>
                        <strong>{{ $motifRibbonOption->color }}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Ukuran:</span>
                        <span class="badge bg-{{ $motifRibbonOption->size_badge_color }}">
                            {{ $motifRibbonOption->size_indonesia }}
                        </span>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Harga Saat Ini:</span>
                        <strong class="text-success">{{ $motifRibbonOption->formatted_price }}</strong>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Digunakan:</span>
                        @if($motifRibbonOption->isUsedInOrders())
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
                        <strong>{{ $motifRibbonOption->orderItems()->distinct('order_id')->count('order_id') }}</strong>
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
                <h6 class="fw-bold mb-3">Panduan Edit Opsi:</h6>
                <ul class="small mb-3">
                    <li class="mb-2">Pastikan kombinasi warna/motif dan ukuran tetap unik</li>
                    <li class="mb-2">Perubahan harga tidak mempengaruhi pesanan lama</li>
                    <li class="mb-2">Opsi yang sudah digunakan tidak bisa dihapus</li>
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
document.getElementById('motifRibbonOptionForm')?.addEventListener('submit', function(e) {
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

document.getElementById('motifRibbonOptionForm')?.addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

document.getElementById('motifRibbonOptionForm')?.addEventListener('submit', function() {
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