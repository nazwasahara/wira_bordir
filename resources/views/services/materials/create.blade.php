@extends('layouts.admin')

@section('title', 'Tambah Material Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-palette me-2"></i>Tambah Material Baru</h2>
    <a href="{{ route('services.materials.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('services.materials.store') }}" method="POST" id="materialForm">
            @csrf
            
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
                                   value="{{ old('name') }}" 
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
                                       value="{{ old('price') }}" 
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
                    </div>
                </div>
            </div>

            <!-- Material Colors Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-swatchbook me-2"></i>Warna Material</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addColorRow()">
                        <i class="fas fa-plus me-1"></i>Tambah Warna
                    </button>
                </div>
                <div class="card-body">
                    <div id="colorsContainer">
                        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Tambahkan variasi warna untuk material ini. Harga warna akan ditambahkan ke harga dasar material.</small>
                        </div>
                        
                        <!-- Color rows will be added here -->
                        <div id="colorRows">
                            <!-- Initial empty state -->
                            <div class="text-center text-muted py-4" id="emptyState">
                                <i class="fas fa-palette fa-3x mb-3"></i>
                                <p>Belum ada warna ditambahkan</p>
                                <button type="button" class="btn btn-outline-primary" onclick="addColorRow()">
                                    <i class="fas fa-plus me-2"></i>Tambah Warna Pertama
                                </button>
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
                            <i class="fas fa-save me-2"></i>Simpan Material
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="{{ route('services.materials.index') }}" class="btn btn-outline-danger">
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
                <h6 class="fw-bold">Tips Menambah Material:</h6>
                <ul class="small">
                    <li class="mb-2">Pastikan nama material unik dan deskriptif</li>
                    <li class="mb-2">Harga dasar adalah harga material tanpa warna</li>
                    <li class="mb-2">Tambahkan berbagai variasi warna yang tersedia</li>
                    <li class="mb-2">Harga warna akan ditambahkan ke harga dasar</li>
                </ul>
                
                <hr>
                
                <h6 class="fw-bold">Contoh:</h6>
                <div class="bg-light p-3 rounded">
                    <small>
                        <strong>Material:</strong> Satin<br>
                        <strong>Harga Dasar:</strong> Rp 50.000<br>
                        <strong>Warna:</strong><br>
                        - Merah (+Rp 5.000)<br>
                        - Biru (+Rp 5.000)<br>
                        - Gold (+Rp 10.000)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.color-row {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    position: relative;
    transition: all 0.3s ease;
}

.color-row:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.color-row-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.color-number {
    background: #0d6efd;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-remove-color {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
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
</style>
@endpush

@push('scripts')
<script>
let colorIndex = 0;

function addColorRow() {
    const emptyState = document.getElementById('emptyState');
    if (emptyState) {
        emptyState.remove();
    }

    colorIndex++;
    const colorRows = document.getElementById('colorRows');
    
    const colorRow = document.createElement('div');
    colorRow.className = 'color-row';
    colorRow.id = `color-row-${colorIndex}`;
    colorRow.innerHTML = `
        <button type="button" class="btn btn-sm btn-danger btn-remove-color" onclick="removeColorRow(${colorIndex})">
            <i class="fas fa-times"></i>
        </button>
        <div class="color-row-header">
            <div class="d-flex align-items-center">
                <div class="color-number me-2">${colorIndex}</div>
                <strong>Warna ${colorIndex}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 mb-2 mb-md-0">
                <label class="form-label small mb-1">Nama Warna</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       name="colors[${colorIndex}][name]" 
                       placeholder="Contoh: Merah, Biru, Gold"
                       required>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Harga Tambahan</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" 
                           class="form-control" 
                           name="colors[${colorIndex}][price]" 
                           placeholder="0"
                           min="0"
                           step="1000"
                           required>
                </div>
            </div>
        </div>
    `;
    
    colorRows.appendChild(colorRow);
    
    // Focus on the new color name input
    colorRow.querySelector('input[type="text"]').focus();
}

function removeColorRow(index) {
    const colorRow = document.getElementById(`color-row-${index}`);
    if (colorRow) {
        colorRow.style.transition = 'all 0.3s ease';
        colorRow.style.opacity = '0';
        colorRow.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            colorRow.remove();
            updateColorNumbers();
            
            // Show empty state if no colors
            const colorRows = document.getElementById('colorRows');
            if (colorRows.children.length === 0) {
                colorRows.innerHTML = `
                    <div class="text-center text-muted py-4" id="emptyState">
                        <i class="fas fa-palette fa-3x mb-3"></i>
                        <p>Belum ada warna ditambahkan</p>
                        <button type="button" class="btn btn-outline-primary" onclick="addColorRow()">
                            <i class="fas fa-plus me-2"></i>Tambah Warna Pertama
                        </button>
                    </div>
                `;
            }
        }, 300);
    }
}

function updateColorNumbers() {
    const colorRows = document.querySelectorAll('.color-row');
    colorRows.forEach((row, index) => {
        const numberBadge = row.querySelector('.color-number');
        const titleText = row.querySelector('strong');
        if (numberBadge && titleText) {
            numberBadge.textContent = index + 1;
            titleText.textContent = `Warna ${index + 1}`;
        }
    });
}

// Form validation
document.getElementById('materialForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Reset form
document.querySelector('button[type="reset"]')?.addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin mereset form ini?')) {
        document.getElementById('colorRows').innerHTML = `
            <div class="text-center text-muted py-4" id="emptyState">
                <i class="fas fa-palette fa-3x mb-3"></i>
                <p>Belum ada warna ditambahkan</p>
                <button type="button" class="btn btn-outline-primary" onclick="addColorRow()">
                    <i class="fas fa-plus me-2"></i>Tambah Warna Pertama
                </button>
            </div>
        `;
        colorIndex = 0;
    }
});

// Auto-format price inputs
document.addEventListener('input', function(e) {
    if (e.target.type === 'number' && e.target.min === '0') {
        if (e.target.value < 0) {
            e.target.value = 0;
        }
    }
});
</script>
@endpush