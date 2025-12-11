@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-box me-2"></i>Detail Produk</h2>
    <div>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Produk
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Product Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="product-icon-large bg-primary text-white mx-auto mb-3">
                    <i class="fas fa-box fa-3x"></i>
                </div>
                <h4 class="mb-1">{{ $product->product_name }}</h4>
                <p class="text-muted mb-2">Harga Dasar</p>
                <h3 class="text-success mb-3">{{ $product->formatted_price }}</h3>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $product->status_badge }}">{{ $product->status_text }}</span>
                    @if($stats['orders_count'] > 0)
                        <span class="badge bg-info">{{ $stats['orders_count'] }} Pesanan</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Produk
                    </a>
                    
                    <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-toggle-{{ $product->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Produk
                        </button>
                    </form>
                    
                    @if(!$product->isUsedInOrders())
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('{{ $product->product_name }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Produk
                    </button>
                    <form id="delete-form" action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @else
                    <div class="alert alert-warning small mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Produk sudah digunakan di pesanan
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Penjualan</h6>
            </div>
            <div class="card-body">
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

    <!-- Product Details -->
    <div class="col-md-8">
        <!-- Product Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Produk</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Produk</small>
                        <strong>#{{ $product->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Produk</small>
                        <strong>{{ $product->product_name }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Harga Dasar</small>
                        <strong class="text-success">{{ $product->formatted_price }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-{{ $product->status_badge }}">{{ $product->status_text }}</span>
                    </div>
                    <div class="col-12 mb-3">
                        <small class="text-muted d-block">Deskripsi</small>
                        <p class="mb-0">{{ $product->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $product->created_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $product->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $product->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $product->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description Detail -->
        @if($product->description)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Deskripsi Lengkap</h6>
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-line;">{{ $product->description }}</p>
            </div>
        </div>
        @endif

        <!-- Sales Statistics -->
        @if($stats['orders_count'] > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Statistik Penjualan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="stat-box">
                            <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                            <h4 class="mb-0">{{ $stats['orders_count'] }}</h4>
                            <small class="text-muted">Total Pesanan</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="stat-box">
                            <i class="fas fa-box fa-2x text-success mb-2"></i>
                            <h4 class="mb-0">{{ $stats['total_sold'] }}</h4>
                            <small class="text-muted">Unit Terjual</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="stat-box">
                            <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                            <h4 class="mb-0">Rp {{ number_format($stats['revenue'] / 1000000, 1) }}Jt</h4>
                            <small class="text-muted">Total Pendapatan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">Belum Ada Penjualan</h6>
                <p class="text-muted small mb-0">Produk ini belum pernah dipesan</p>
            </div>
        </div>
        @endif
    </div>
</div>

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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Produk</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus produk:</p>
                <p class="fw-bold mb-3" id="deleteProductName"></p>
                <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Tindakan ini tidak dapat dibatalkan!</small>
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
.product-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-box {
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.stat-box:hover {
    background: #e9ecef;
    transform: translateY(-2px);
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
</style>
@endpush

@push('scripts')
<script>
function showDeleteModal(productName) {
    document.getElementById('deleteProductName').textContent = productName;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
    this.disabled = true;
    document.getElementById('delete-form').submit();
});

document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush