@extends('layouts.admin')

@section('title', 'Detail Jenis Selempang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-tshirt me-2"></i>Detail Jenis Selempang</h2>
    <div>
        <a href="{{ route('services.sash-types.edit', $sashType) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Tipe
        </a>
        <a href="{{ route('services.sash-types.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Sash Type Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="sash-type-icon-large bg-primary text-white mx-auto mb-3">
                    <i class="fas fa-tshirt fa-3x"></i>
                </div>
                <h4 class="mb-1">{{ $sashType->name }}</h4>
                <p class="text-muted mb-2">Harga Dasar</p>
                <h3 class="text-success mb-3">{{ $sashType->formatted_price }}</h3>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($stats['orders_count'] > 0)
                        <span class="badge bg-success">Digunakan</span>
                    @else
                        <span class="badge bg-warning">Belum Digunakan</span>
                    @endif
                    @if($stats['orders_count'] > 50)
                        <span class="badge bg-info">Populer</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('services.sash-types.edit', $sashType) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Tipe
                    </a>
                    
                    @if(!$sashType->isUsedInOrders())
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('{{ $sashType->name }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Tipe
                    </button>
                    <form id="delete-form" action="{{ route('services.sash-types.destroy', $sashType) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @else
                    <div class="alert alert-warning small mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Tipe sudah digunakan di pesanan
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
                    <small class="text-muted d-block">Total Pesanan</small>
                    <strong>{{ $stats['orders_count'] }} Pesanan</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Terjual</small>
                    <strong>{{ $stats['total_used'] }} Unit</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Total Pendapatan</small>
                    <strong class="text-success">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Sash Type Details -->
    <div class="col-md-8">
        <!-- Sash Type Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Jenis Selempang</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Tipe</small>
                        <strong>#{{ $sashType->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Tipe</small>
                        <strong>{{ $sashType->name }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Harga Dasar</small>
                        <strong class="text-success">{{ $sashType->formatted_price }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Status</small>
                        @if($stats['orders_count'] > 0)
                            <span class="badge bg-success">Aktif Digunakan</span>
                        @else
                            <span class="badge bg-secondary">Belum Digunakan</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $sashType->created_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $sashType->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $sashType->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $sashType->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Information -->
        @if($stats['orders_count'] > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Informasi Penggunaan</h6>
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
                            <h4 class="mb-0">{{ $stats['total_used'] }}</h4>
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Tipe</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus Jenis Selempang:</p>
                <p class="fw-bold mb-3" id="deleteSashTypeName"></p>
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
.sash-type-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sash-preview-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
}

.sash-preview-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
}

.sash-illustration {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sash-shape {
    width: 60px;
    height: 100px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    position: relative;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sash-shape::before {
    content: '';
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 2px;
    background: rgba(255,255,255,0.5);
}

.sash-shape::after {
    content: '';
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 2px;
    background: rgba(255,255,255,0.5);
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
function showDeleteModal(sashTypeName) {
    document.getElementById('deleteSashTypeName').textContent = sashTypeName;
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