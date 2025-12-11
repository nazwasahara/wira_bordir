@extends('layouts.admin')

@section('title', 'Detail Material')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-palette me-2"></i>Detail Material</h2>
    <div>
        <a href="{{ route('services.materials.edit', $material) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Material
        </a>
        <a href="{{ route('services.materials.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Material Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="material-icon-large bg-primary text-white mx-auto mb-3">
                    <i class="fas fa-palette fa-3x"></i>
                </div>
                <h4 class="mb-1">{{ $material->name }}</h4>
                <p class="text-muted mb-2">Harga Dasar</p>
                <h3 class="text-success mb-3">{{ $material->formatted_price }}</h3>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-info">{{ $material->colors_count }} Warna</span>
                    @if($stats['orders_count'] > 0)
                        <span class="badge bg-success">Digunakan</span>
                    @else
                        <span class="badge bg-warning">Belum Digunakan</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('services.materials.edit', $material) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Material
                    </a>
                    
                    @if(!$material->isUsedInOrders())
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('{{ $material->name }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Material
                    </button>
                    <form id="delete-form" action="{{ route('services.materials.destroy', $material) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @else
                    <div class="alert alert-warning small mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Material sudah digunakan di pesanan
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Jumlah Warna</small>
                    <strong>{{ $stats['colors_count'] }} Warna</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Pesanan</small>
                    <strong>{{ $stats['orders_count'] }} Pesanan</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Terjual</small>
                    <strong>{{ $stats['total_sold'] }} Unit</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Total Pendapatan</small>
                    <strong class="text-success">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Details -->
    <div class="col-md-8">
        <!-- Material Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Material</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Material</small>
                        <strong>#{{ $material->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Material</small>
                        <strong>{{ $material->name }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Harga Dasar</small>
                        <strong class="text-success">{{ $material->formatted_price }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Jumlah Warna</small>
                        <span class="badge bg-info">{{ $material->colors_count }} Warna</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $material->created_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $material->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $material->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $material->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Colors -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-swatchbook me-2"></i>Daftar Warna ({{ $material->colors_count }})</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addColorModal">
                    <i class="fas fa-plus me-1"></i>Tambah Warna
                </button>
            </div>
            <div class="card-body p-0">
                @if($material->colors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Warna</th>
                                <th>Harga Tambahan</th>
                                <th>Total Harga</th>
                                <th>Dibuat</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($material->colors as $color)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-dot me-2"></div>
                                        <strong>{{ $color->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $color->formatted_price }}</td>
                                <td><strong class="text-success">{{ $color->formatted_total_price }}</strong></td>
                                <td>{{ $color->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                onclick="editColor({{ $color->id }}, '{{ $color->name }}', {{ $color->price }})"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(!$color->isUsedInOrders())
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="showDeleteColorModal({{ $color->id }}, '{{ $color->name }}')"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Belum ada warna ditambahkan</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addColorModal">
                        <i class="fas fa-plus me-2"></i>Tambah Warna Pertama
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Color Modal -->
<div class="modal fade" id="addColorModal" tabindex="-1" aria-labelledby="addColorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="addColorModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Warna Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('services.materials.colors.store', $material) }}" method="POST" id="addColorForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="color_name" class="form-label">
                            Nama Warna <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="color_name" 
                               name="name" 
                               placeholder="Contoh: Merah, Biru, Gold"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="color_price" class="form-label">
                            Harga Tambahan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="color_price" 
                                   name="price" 
                                   placeholder="0"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                        <small class="text-muted">Harga ini akan ditambahkan ke harga dasar material ({{ $material->formatted_price }})</small>
                    </div>
                    <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                        <i class="fas fa-calculator me-2"></i>
                        <div>
                            <small><strong>Total Harga:</strong> <span id="totalPricePreview">-</span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="addColorBtn">
                        <i class="fas fa-save me-2"></i>Simpan Warna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Color Modal -->
<div class="modal fade" id="editColorModal" tabindex="-1" aria-labelledby="editColorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="editColorModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Warna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editColorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_color_name" class="form-label">
                            Nama Warna <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_color_name" 
                               name="name" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_color_price" class="form-label">
                            Harga Tambahan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="edit_color_price" 
                                   name="price" 
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                        <i class="fas fa-calculator me-2"></i>
                        <div>
                            <small><strong>Total Harga:</strong> <span id="editTotalPricePreview">-</span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="editColorBtn">
                        <i class="fas fa-save me-2"></i>Update Warna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Material Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                    </div>
                </div>
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Material</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus material:</p>
                <p class="fw-bold mb-3" id="deleteMaterialName"></p>
                <div class="alert alert-danger d-flex align-items-start mb-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div class="text-start">
                        <small class="fw-bold d-block mb-1">Peringatan:</small>
                        <small>Semua warna dari material ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan!</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Color Modal -->
<div class="modal fade" id="deleteColorModal" tabindex="-1" aria-labelledby="deleteColorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                    </div>
                </div>
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Warna</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus warna:</p>
                <p class="fw-bold mb-3" id="deleteColorName"></p>
                <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Tindakan ini tidak dapat dibatalkan!</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteColorBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.material-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.color-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.delete-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
    }
    70% {
        box-shadow: 0 0 0 20px rgba(255, 193, 7, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

.modal-content {
    border-radius: 16px;
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
const materialBasePrice = {{ $material->price }};

// Add Color Form - Calculate total price preview
document.getElementById('color_price')?.addEventListener('input', function() {
    const colorPrice = parseFloat(this.value) || 0;
    const totalPrice = materialBasePrice + colorPrice;
    document.getElementById('totalPricePreview').textContent = 
        'Rp ' + totalPrice.toLocaleString('id-ID');
});

// Add Color Form Submit
document.getElementById('addColorForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('addColorBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    btn.disabled = true;
});

// Edit Color Function
let editColorId = null;

function editColor(colorId, colorName, colorPrice) {
    editColorId = colorId;
    document.getElementById('edit_color_name').value = colorName;
    document.getElementById('edit_color_price').value = colorPrice;
    
    // Update form action
    const form = document.getElementById('editColorForm');
    form.action = `/admin/materials/{{ $material->id }}/colors/${colorId}`;
    
    // Calculate initial total
    const totalPrice = materialBasePrice + colorPrice;
    document.getElementById('editTotalPricePreview').textContent = 
        'Rp ' + totalPrice.toLocaleString('id-ID');
    
    // Show modal
    const editModal = new bootstrap.Modal(document.getElementById('editColorModal'));
    editModal.show();
}

// Edit Color Price Preview
document.getElementById('edit_color_price')?.addEventListener('input', function() {
    const colorPrice = parseFloat(this.value) || 0;
    const totalPrice = materialBasePrice + colorPrice;
    document.getElementById('editTotalPricePreview').textContent = 
        'Rp ' + totalPrice.toLocaleString('id-ID');
});

// Edit Color Form Submit
document.getElementById('editColorForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('editColorBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    btn.disabled = true;
});

// Delete Material Modal
function showDeleteModal(materialName) {
    document.getElementById('deleteMaterialName').textContent = materialName;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
    this.disabled = true;
    document.getElementById('delete-form').submit();
});

// Delete Color Modal
let deleteColorId = null;

function showDeleteColorModal(colorId, colorName) {
    deleteColorId = colorId;
    document.getElementById('deleteColorName').textContent = colorName;
    const deleteColorModal = new bootstrap.Modal(document.getElementById('deleteColorModal'));
    deleteColorModal.show();
}

document.getElementById('confirmDeleteColorBtn')?.addEventListener('click', function() {
    if (deleteColorId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        
        // Create and submit delete form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/materials/{{ $material->id }}/colors/${deleteColorId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
});

// Reset modals on close
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', function() {
        const submitBtns = this.querySelectorAll('button[type="submit"]');
        submitBtns.forEach(btn => {
            btn.disabled = false;
            const icon = btn.querySelector('i');
            if (icon && icon.classList.contains('fa-spinner')) {
                icon.classList.remove('fa-spinner', 'fa-spin');
                icon.classList.add('fa-save');
                btn.querySelector('span')?.textContent || (btn.textContent = btn.id.includes('add') ? 'Simpan Warna' : 'Update Warna');
            }
        });
    });
});
</script>
@endpush