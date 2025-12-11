@extends('layouts.admin')

@section('title', 'Tambah Jenis Selempang Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-tshirt me-2"></i>Tambah Jenis Selempang Baru</h2>
    <a href="{{ route('services.sash-types.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.sash-types.store') }}" method="POST" id="sashTypeForm">
            @csrf
            
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
                                   value="{{ old('name') }}" 
                                   placeholder="Contoh: Model Standar, Model Premium, Model Ekslusif, dll"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Masukkan nama jenis/model selempang</small>
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
                            <small class="text-muted">Harga dasar untuk tipe ini</small>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fas fa-lightbulb me-2"></i>
                                <div>
                                    <small><strong>Tips:</strong> Jenis Selempang menentukan model dasar. Harga ini menjadi base price sebelum ditambah customisasi lainnya.</small>
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
                            <i class="fas fa-save me-2"></i>Simpan Jenis Selempang
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="{{ route('services.sash-types.index') }}" class="btn btn-outline-danger">
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
                <h6 class="fw-bold">Tips Menambah Jenis Selempang:</h6>
                <ul class="small">
                    <li class="mb-2">Tentukan nama tipe yang jelas dan mudah dipahami</li>
                    <li class="mb-2">Harga dasar mencakup material dan pembuatan standar</li>
                    <li class="mb-2">Tipe berbeda bisa memiliki ukuran atau style berbeda</li>
                    <li class="mb-2">Nama tipe harus unik dalam sistem</li>
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
document.getElementById('sashTypeForm')?.addEventListener('submit', function(e) {
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

// Auto capitalize sash type name
const nameInput = document.getElementById('name');
nameInput?.addEventListener('blur', function() {
    this.value = this.value.split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
});
</script>
@endpush