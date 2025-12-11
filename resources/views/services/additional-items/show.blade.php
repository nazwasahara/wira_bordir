@extends('layouts.admin')

@section('title', 'Detail Item Tambahan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Detail Item Tambahan</h2>
    <div>
        <a href="{{ route('services.additional-items.edit', $additionalItem) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Item
        </a>
        <a href="{{ route('services.additional-items.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Item Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="item-icon-large bg-primary text-white mx-auto mb-3">
                    <i class="fas fa-plus-circle fa-3x"></i>
                </div>
                <h4 class="mb-1">{{ $additionalItem->name }}</h4>
                <p class="text-muted mb-2">Harga Dasar</p>
                <h3 class="text-success mb-3">{{ $additionalItem->formatted_price }}</h3>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-info">{{ $stats['options_count'] }} Opsi</span>
                    @if($stats['orders_count'] > 0)
                        <span class="badge bg-success">Digunakan</span>
                    @else
                        <span class="badge bg-warning">Belum Digunakan</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('services.additional-items.edit', $additionalItem) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Item
                    </a>
                    
                    @if(!$additionalItem->isUsedInOrders())
                    <button type="button" class="btn btn-danger" onclick="showDeleteItemModal('{{ $additionalItem->name }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Item
                    </button>
                    <form id="delete-item-form" action="{{ route('services.additional-items.destroy', $additionalItem) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @else
                    <div class="alert alert-warning small mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Item sudah digunakan di pesanan
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
                    <small class="text-muted d-block">Total Opsi</small>
                    <strong>{{ $stats['options_count'] }} Opsi</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Pesanan</small>
                    <strong>{{ $stats['orders_count'] }} Pesanan</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Total Pendapatan</small>
                    <strong class="text-success">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Details & Options -->
    <div class="col-md-8">
        <!-- Item Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Item</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Item</small>
                        <strong>#{{ $additionalItem->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Item</small>
                        <strong>{{ $additionalItem->name }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Harga Dasar</small>
                        <strong class="text-success">{{ $additionalItem->formatted_price }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Rentang Harga Opsi</small>
                        <strong class="text-primary">{{ $additionalItem->price_range }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $additionalItem->created_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $additionalItem->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $additionalItem->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $additionalItem->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Options Management -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Opsi ({{ $stats['options_count'] }})</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addOptionModal">
                    <i class="fas fa-plus me-1"></i>Tambah Opsi
                </button>
            </div>
            <div class="card-body p-0">
                @if($additionalItem->options->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Warna</th>
                                <th>Model</th>
                                <th>Harga</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($additionalItem->options as $option)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $option->color }}</strong></td>
                                <td>{{ $option->model }}</td>
                                <td><strong class="text-success">{{ $option->formatted_price }}</strong></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                onclick="showEditOptionModal({{ $option->id }}, '{{ $option->color }}', '{{ $option->model }}', {{ $option->price }})"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="showDeleteOptionModal({{ $option->id }}, '{{ $option->full_name }}')"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-option-form-{{ $option->id }}" 
                                          action="{{ route('services.additional-items.options.destroy', [$additionalItem, $option]) }}" 
                                          method="POST" 
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Belum ada opsi untuk item ini</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOptionModal">
                        <i class="fas fa-plus me-2"></i>Tambah Opsi Pertama
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Option Modal -->
<div class="modal fade" id="addOptionModal" tabindex="-1" aria-labelledby="addOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('services.additional-items.options.store', $additionalItem) }}" method="POST" id="addOptionForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addOptionModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Opsi Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_color" class="form-label">Warna <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="add_color" 
                               name="color" 
                               placeholder="Contoh: Gold, Silver, Merah"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="add_model" class="form-label">Model <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="add_model" 
                               name="model" 
                               placeholder="Contoh: Standar, Premium, Eksklusif"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="add_price" class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="add_price" 
                                   name="price" 
                                   placeholder="0"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Opsi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Option Modal -->
<div class="modal fade" id="editOptionModal" tabindex="-1" aria-labelledby="editOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form id="editOptionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editOptionModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Opsi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_color" class="form-label">Warna <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_color" 
                               name="color" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_model" class="form-label">Model <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_model" 
                               name="model" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Harga <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="edit_price" 
                                   name="price" 
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Opsi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Item Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="delete-icon-wrapper mb-3">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Item</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus item:</p>
                <p class="fw-bold mb-3" id="deleteItemName"></p>
                <div class="alert alert-danger d-flex align-items-center mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <small>Semua opsi dari item ini juga akan dihapus!</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4" onclick="document.getElementById('delete-item-form').submit()">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Option Modal -->
<div class="modal fade" id="deleteOptionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="delete-icon-wrapper mb-3">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Opsi</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus opsi:</p>
                <p class="fw-bold mb-3" id="deleteOptionName"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteOptionBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.item-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
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
}
</style>
@endpush

@push('scripts')
<script>
// Add Option Form Submit
document.getElementById('addOptionForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Show Edit Option Modal
function showEditOptionModal(optionId, color, model, price) {
    const form = document.getElementById('editOptionForm');
    form.action = '{{ route("services.additional-items.options.update", [$additionalItem, ":id"]) }}'.replace(':id', optionId);
    
    document.getElementById('edit_color').value = color;
    document.getElementById('edit_model').value = model;
    document.getElementById('edit_price').value = price;
    
    const modal = new bootstrap.Modal(document.getElementById('editOptionModal'));
    modal.show();
}

// Edit Option Form Submit
document.getElementById('editOptionForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Show Delete Item Modal
function showDeleteItemModal(itemName) {
    document.getElementById('deleteItemName').textContent = itemName;
    const modal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
    modal.show();
}

// Show Delete Option Modal
let deleteOptionId = null;

function showDeleteOptionModal(optionId, optionName) {
    deleteOptionId = optionId;
    document.getElementById('deleteOptionName').textContent = optionName;
    const modal = new bootstrap.Modal(document.getElementById('deleteOptionModal'));
    modal.show();
}

document.getElementById('confirmDeleteOptionBtn')?.addEventListener('click', function() {
    if (deleteOptionId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        document.getElementById('delete-option-form-' + deleteOptionId).submit();
    }
});
</script>
@endpush