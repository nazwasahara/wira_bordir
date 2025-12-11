@extends('layouts.admin')

@section('title', 'Tambah Opsi Pita Motif Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-wind me-2"></i>Tambah Opsi Pita Motif Baru</h2>
    <a href="{{ route('services.motif-ribbon-options.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.motif-ribbon-options.store') }}" method="POST" id="motifRibbonOptionForm">
            @csrf
            
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
                                   value="{{ old('color') }}" 
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
                                    <option value="{{ $key }}" {{ old('size') == $key ? 'selected' : '' }}>
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
                                       value="{{ old('price', 0) }}" 
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

                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-lightbulb me-2"></i>
                                <div>
                                    <small><strong>Tips:</strong> Pita motif adalah pita dengan corak/pattern seperti polkadot, garis, bunga, dll. Motif lebih rumit biasanya lebih mahal.</small>
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
                            <i class="fas fa-save me-2"></i>Simpan Opsi Pita Motif
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="{{ route('services.motif-ribbon-options.index') }}" class="btn btn-outline-danger">
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
                <h6 class="fw-bold">Tips Menambah Opsi Pita Motif:</h6>
                <ul class="small">
                    <li class="mb-2">Kombinasi warna/motif dan ukuran harus unik</li>
                    <li class="mb-2">Pita kecil untuk aksen halus</li>
                    <li class="mb-2">Pita sedang untuk dekorasi standar</li>
                    <li class="mb-2">Pita besar untuk tampilan mencolok</li>
                </ul>
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
// Form validation and submit handling
document.getElementById('motifRibbonOptionForm')?.addEventListener('submit', function(e) {
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
const colorInput = document.getElementById('color');
colorInput?.addEventListener('blur', function() {
    this.value = this.value.split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
});
</script>
@endpush