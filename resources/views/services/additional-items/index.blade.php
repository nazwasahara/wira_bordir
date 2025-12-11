@extends('layouts.admin')

@section('title', 'Manajemen Item Tambahan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Manajemen Item Tambahan</h2>
    <a href="{{ route('services.additional-items.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Item Baru
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Item</h6>
                <h3 class="mb-0 fw-bold">{{ $stats['total_items'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Opsi</h6>
                <h3 class="mb-0 fw-bold text-info">{{ $stats['total_options'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Digunakan</h6>
                <h3 class="mb-0 fw-bold text-success">{{ $stats['used_in_orders'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Nilai</h6>
                <h3 class="mb-0 fw-bold text-primary">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('services.additional-items.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari berdasarkan nama item..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama Item</option>
                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Harga</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutan</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('services.additional-items.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Additional Items Grid -->
<div class="row g-3">
    @forelse($additionalItems as $item)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="item-icon bg-primary text-white">
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('services.additional-items.show', $item) }}">
                                    <i class="fas fa-eye me-2"></i>Lihat Detail
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('services.additional-items.edit', $item) }}">
                                    <i class="fas fa-edit me-2"></i>Edit Item
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item text-danger" 
                                        onclick="showDeleteModal({{ $item->id }}, '{{ $item->name }}')">
                                    <i class="fas fa-trash me-2"></i>Hapus Item
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <h5 class="card-title mb-2">{{ $item->name }}</h5>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Harga Dasar</small>
                    <strong class="text-success">{{ $item->formatted_price }}</strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Jumlah Opsi</small>
                    <span class="badge bg-info">{{ $item->options_count }} Opsi</span>
                </div>

                @if($item->options_count > 0)
                <div class="mb-3">
                    <small class="text-muted d-block">Rentang Harga Opsi</small>
                    <small class="text-primary">{{ $item->price_range }}</small>
                </div>
                @endif

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('services.additional-items.show', $item) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-eye me-1"></i>Detail
                    </a>
                    <a href="{{ route('services.additional-items.edit', $item) }}" class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
            </div>

            <form id="delete-form-{{ $item->id }}" 
                  action="{{ route('services.additional-items.destroy', $item) }}" 
                  method="POST" 
                  class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-plus-circle fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada item tambahan ditemukan</h5>
                <p class="text-muted mb-3">Mulai dengan menambahkan item tambahan pertama Anda</p>
                <a href="{{ route('services.additional-items.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Item Baru
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($additionalItems->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $additionalItems->links() }}
</div>
@endif

<!-- Delete Confirmation Modal -->
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus item tambahan:</p>
                <p class="fw-bold mb-3" id="deleteItemName"></p>
                <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Semua opsi dari item ini juga akan dihapus! Tindakan ini tidak dapat dibatalkan!</small>
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
@endsection

@push('styles')
<style>
.item-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
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
</style>
@endpush

@push('scripts')
<script>
let deleteItemId = null;

function showDeleteModal(itemId, itemName) {
    deleteItemId = itemId;
    document.getElementById('deleteItemName').textContent = itemName;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    if (deleteItemId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        document.getElementById('delete-form-' + deleteItemId).submit();
    }
});

document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    deleteItemId = null;
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush