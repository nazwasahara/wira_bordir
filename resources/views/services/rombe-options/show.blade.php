@extends('layouts.admin')

@section('title', 'Detail Opsi Rombe')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-gem me-2"></i>Detail Opsi Rombe</h2>
    <div>
        <a href="{{ route('services.rombe-options.edit', $rombeOption) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Opsi
        </a>
        <a href="{{ route('services.rombe-options.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Rombe Option Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="rombe-icon-large bg-primary text-white mx-auto mb-3">
                    <i class="fas fa-gem fa-3x"></i>
                </div>
                <h4 class="mb-1">{{ $rombeOption->color }}</h4>
                <p class="text-muted mb-2">Ukuran: {{ $rombeOption->size_indonesia }}</p>
                <h3 class="text-success mb-3">{{ $rombeOption->formatted_price }}</h3>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $rombeOption->size_badge_color }}">
                        {{ $rombeOption->size_indonesia }}
                    </span>
                    @if($stats['orders_count'] > 0)
                        <span class="badge bg-success">Digunakan</span>
                    @else
                        <span class="badge bg-warning">Belum Digunakan</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('services.rombe-options.edit', $rombeOption) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Opsi
                    </a>
                    
                    @if(!$rombeOption->isUsedInOrders())
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('{{ $rombeOption->color }} - {{ $rombeOption->size_indonesia }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Opsi
                    </button>
                    <form id="delete-form" action="{{ route('services.rombe-options.destroy', $rombeOption) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @else
                    <div class="alert alert-warning small mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Opsi sudah digunakan di pesanan
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
                    <small class="text-muted d-block">Total Digunakan</small>
                    <strong>{{ $stats['total_used'] }} Kali</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Total Pendapatan</small>
                    <strong class="text-success">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Rombe Option Details -->
    <div class="col-md-8">
        <!-- Rombe Option Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Opsi Rombe</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Opsi</small>
                        <strong>#{{ $rombeOption->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Warna</small>
                        <strong>{{ $rombeOption->color }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Ukuran</small>
                        <span class="badge bg-{{ $rombeOption->size_badge_color }}">
                            {{ $rombeOption->size_indonesia }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Harga</small>
                        <strong class="text-success">{{ $rombeOption->formatted_price }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $rombeOption->created_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $rombeOption->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $rombeOption->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $rombeOption->updated_at->diffForHumans() }}</small>
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
                            <i class="fas fa-chart-bar fa-2x text-success mb-2"></i>
                            <h4 class="mb-0">{{ $stats['total_used'] }}</h4>
                            <small class="text-muted">Kali Digunakan</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="stat-box">
                            <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                            <h4 class="mb-0">Rp {{ number_format($stats['revenue'] / 1000, 1) }}rb</h4>
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Opsi</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus opsi rombe:</p>
                <p class="fw-bold mb-3" id="deleteRombeOptionName"></p>
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
.rombe-icon-large {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rombe-preview-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 2rem;
}

.rombe-sample {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.rombe-shape {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: rotate(45deg);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    position: relative;
}

.rombe-shape::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
}

.rombe-sample[data-size="small"] .rombe-shape {
    width: 25px;
    height: 25px;
}

.rombe-sample[data-size="medium"] .rombe-shape {
    width: 40px;
    height: 40px;
}

.rombe-sample[data-size="large"] .rombe-shape {
    width: 55px;
    height: 55px;
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
function showDeleteModal(rombeOptionName) {
    document.getElementById('deleteRombeOptionName').textContent = rombeOptionName;
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