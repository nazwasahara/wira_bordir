@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-box me-2"></i>Tambah Produk Baru</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.products.store') }}" method="POST" id="productForm">
            @csrf
            
            <!-- Product Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Produk</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">
                            Nama Produk <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('product_name') is-invalid @enderror" 
                               id="product_name" 
                               name="product_name" 
                               value="{{ old('product_name') }}" 
                               placeholder="Contoh: Selempang Custom Premium"
                               required>
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Masukkan nama produk yang jelas dan deskriptif</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Deskripsi Produk
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Deskripsi lengkap tentang produk ini...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Jelaskan detail produk, keunggulan, dan spesifikasi (opsional)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="base_price" class="form-label">
                                Harga Dasar <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       class="form-control @error('base_price') is-invalid @enderror" 
                                       id="base_price" 
                                       name="base_price" 
                                       value="{{ old('base_price', 0) }}" 
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Harga dasar sebelum customisasi</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('is_active') is-invalid @enderror" 
                                    id="is_active" 
                                    name="is_active" 
                                    required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Produk aktif akan tampil di katalog</small>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-lightbulb me-2"></i>
                        <div>
                            <small><strong>Tips:</strong> Harga dasar adalah harga awal produk. Harga final akan ditambahkan dengan biaya customisasi (material, font, aksesori, dll).</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Produk
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-danger">
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
                <h6 class="fw-bold">Tips Menambah Produk:</h6>
                <ul class="small">
                    <li class="mb-2">Gunakan nama produk yang jelas dan mudah dipahami</li>
                    <li class="mb-2">Deskripsi membantu pelanggan memahami produk</li>
                    <li class="mb-2">Harga dasar bisa 0 jika harga tergantung customisasi</li>
                    <li class="mb-2">Produk nonaktif tidak akan tampil di katalog</li>
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
document.getElementById('productForm')?.addEventListener('submit', function(e) {
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
const priceInput = document.getElementById('base_price');
priceInput?.addEventListener('input', function() {
    if (this.value < 0) {
        this.value = 0;
    }
});

// Auto capitalize product name
const nameInput = document.getElementById('product_name');
nameInput?.addEventListener('blur', function() {
    this.value = this.value.split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
});

// Character counter for description
const descriptionTextarea = document.getElementById('description');
if (descriptionTextarea) {
    const maxLength = 500;
    const counter = document.createElement('small');
    counter.className = 'text-muted d-block mt-1';
    descriptionTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = descriptionTextarea.value.length;
        counter.textContent = `${length}/${maxLength} karakter`;
        if (length > maxLength * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    }
    
    descriptionTextarea.addEventListener('input', updateCounter);
    updateCounter();
}
</script>
@endpush