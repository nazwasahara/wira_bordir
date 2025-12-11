@extends('layouts.admin')

@section('title', 'Detail Warna Pita')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-ribbon me-2"></i>Detail Warna Pita</h2>
    <div>
        <a href="{{ route('services.ribbon-colors.edit', $ribbonColor) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Warna
        </a>
        <a href="{{ route('services.ribbon-colors.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Ribbon Color Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="ribbon-color-preview-large mx-auto mb-3" style="background: {{ getColorCode($ribbonColor->name) }}">
                    <i class="fas fa-ribbon fa-3x text-white"></i>
                </div>
                <h4 class="mb-1">{{ $ribbonColor->name }}</h4>
                <p class="text-muted mb-2">Harga Warna</p>
                <h3 class="text-success mb-3">{{ $ribbonColor->formatted_price }}</h3>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($stats['orders_count'] > 0)
                        <span class="badge bg-success">Digunakan</span>
                    @else
                        <span class="badge bg-warning">Belum Digunakan</span>
                    @endif
                    @if($ribbonColor->price == 0)
                        <span class="badge bg-info">Standar</span>
                    @else
                        <span class="badge bg-primary">Premium</span>
                    @endif
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('services.ribbon-colors.edit', $ribbonColor) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Warna
                    </a>
                    
                    @if(!$ribbonColor->isUsedInOrders())
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('{{ $ribbonColor->name }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Warna
                    </button>
                    <form id="delete-form" action="{{ route('services.ribbon-colors.destroy', $ribbonColor) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @else
                    <div class="alert alert-warning small mb-0" role="alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Warna sudah digunakan di pesanan
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

    <!-- Ribbon Color Details -->
    <div class="col-md-8">
        <!-- Ribbon Color Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Warna Pita</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Warna</small>
                        <strong>#{{ $ribbonColor->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Warna</small>
                        <strong>{{ $ribbonColor->name }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Harga</small>
                        <strong class="text-success">{{ $ribbonColor->formatted_price }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Tipe</small>
                        @if($ribbonColor->price == 0)
                            <span class="badge bg-info">Warna Standar</span>
                        @else
                            <span class="badge bg-primary">Warna Premium</span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $ribbonColor->created_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $ribbonColor->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $ribbonColor->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $ribbonColor->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Preview -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Preview Warna</h6>
            </div>
            <div class="card-body">
                <div class="color-swatches">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="color-swatch-card text-center">
                                <div class="color-swatch" style="background: {{ getColorCode($ribbonColor->name) }}"></div>
                                <small class="d-block mt-2 text-muted">Preview Warna</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="color-swatch-card text-center">
                                <div class="color-swatch" style="background: linear-gradient(135deg, {{ getColorCode($ribbonColor->name) }} 0%, {{ adjustBrightness(getColorCode($ribbonColor->name), -20) }} 100%)"></div>
                                <small class="d-block mt-2 text-muted">Gradient</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="color-swatch-card text-center">
                                <div class="color-swatch" style="background: {{ getColorCode($ribbonColor->name) }}; opacity: 0.7;"></div>
                                <small class="d-block mt-2 text-muted">Transparansi</small>
                            </div>
                        </div>
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Warna</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus warna pita:</p>
                <p class="fw-bold mb-3" id="deleteRibbonColorName"></p>
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
.ribbon-color-preview-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.color-swatch {
    width: 100%;
    height: 100px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.color-swatch-card {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.color-swatch-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
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
// Delete Modal
function showDeleteModal(ribbonColorName) {
    document.getElementById('deleteRibbonColorName').textContent = ribbonColorName;
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

@php
// Helper function untuk mapping warna
function getColorCode($colorName) {
    $colors = [
        'Merah' => '#DC143C',
        'Biru' => '#1E90FF',
        'Hijau' => '#32CD32',
        'Kuning' => '#FFD700',
        'Hitam' => '#000000',
        'Putih' => '#FFFFFF',
        'Pink' => '#FF69B4',
        'Ungu' => '#9370DB',
        'Orange' => '#FF8C00',
        'Coklat' => '#8B4513',
        'Abu-abu' => '#808080',
        'Gold' => '#FFD700',
        'Silver' => '#C0C0C0',
        'Navy' => '#000080',
        'Tosca' => '#40E0D0',
        'Maroon' => '#800000',
        'Cream' => '#FFFDD0',
        'Rose Gold' => '#B76E79',
    ];
    
    return $colors[$colorName] ?? '#999999';
}

// Helper function untuk adjust brightness
function adjustBrightness($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) 
               . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) 
               . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}
@endphp