@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-user me-2"></i>Detail Pengguna</h2>
    <div>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Pengguna
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- User Info Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center">
                <div class="avatar-circle bg-{{ $user->role_badge_color }} text-white mx-auto mb-3" style="width: 100px; height: 100px; font-size: 36px;">
                    {{ strtoupper(substr($user->username, 0, 2)) }}
                </div>
                <h4 class="mb-1">{{ $user->username }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-{{ $user->role_badge_color }}">
                        @if($user->role == 'admin')
                            Admin
                        @elseif($user->role == 'owner')
                            Pemilik
                        @else
                            Pelanggan
                        @endif
                    </span>
                    <span class="badge bg-{{ $user->status_badge_color }}">
                        {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} me-2"></i>
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Pengguna
                        </button>
                    </form>
                    
                    @if($user->id !== auth()->id())
                    <button type="button" class="btn btn-danger" onclick="showDeleteModal('{{ $user->username }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Pengguna
                    </button>
                    <form id="delete-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-address-card me-2"></i>Informasi Kontak</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Telepon</small>
                    <strong>{{ $user->phone_number }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <strong>{{ $user->email }}</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Alamat</small>
                    <p class="mb-0">{{ $user->address }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity & Statistics -->
    <div class="col-md-8">
        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ $userStats['total_orders'] }}</h4>
                        <small class="text-muted">Total Pesanan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ $userStats['completed_orders'] }}</h4>
                        <small class="text-muted">Selesai</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ $userStats['pending_orders'] }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ number_format($userStats['total_spent'], 0) }}</h4>
                        <small class="text-muted">Total Belanja</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">ID Pengguna</small>
                        <strong>#{{ $user->id }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Username</small>
                        <strong>{{ $user->username }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Role</small>
                        <span class="badge bg-{{ $user->role_badge_color }}">
                            @if($user->role == 'admin')
                                Admin
                            @elseif($user->role == 'owner')
                                Pemilik
                            @else
                                Pelanggan
                            @endif
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Status Akun</small>
                        <span class="badge bg-{{ $user->status_badge_color }}">
                            {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Email Terverifikasi</small>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Terverifikasi
                            </span>
                            <small class="d-block text-muted">{{ $user->email_verified_at->format('d M Y H:i') }}</small>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-times me-1"></i>Belum Terverifikasi
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terdaftar Sejak</small>
                        <strong>{{ $user->created_at->format('d M Y') }}</strong>
                        <small class="d-block text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Terakhir Diperbarui</small>
                        <strong>{{ $user->updated_at->format('d M Y H:i') }}</strong>
                        <small class="d-block text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Pesanan Terkini</h6>
                <a href="#" class="text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($user->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders->take(5) as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge_color }}">
                                        @if($order->status == 'pending')
                                            Pending
                                        @elseif($order->status == 'processing')
                                            Diproses
                                        @elseif($order->status == 'completed')
                                            Selesai
                                        @elseif($order->status == 'cancelled')
                                            Dibatalkan
                                        @else
                                            {{ ucfirst($order->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada pesanan</p>
                </div>
                @endif
            </div>
        </div>
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus pengguna:</p>
                <p class="fw-bold mb-2" id="deleteUsername"></p>
                <div class="alert alert-danger d-flex align-items-start mb-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div class="text-start">
                        <small class="fw-bold d-block mb-1">Peringatan:</small>
                        <small>Tindakan ini akan menghapus semua data pengguna termasuk riwayat pesanan dan tidak dapat dibatalkan!</small>
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
@endsection

@push('styles')
<style>
.avatar-circle {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* Delete Modal Styling */
#deleteModal .modal-content {
    border-radius: 16px;
    overflow: hidden;
}

#deleteModal .modal-header {
    background: transparent;
}

#deleteModal .modal-body {
    padding: 2rem;
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

#deleteModal .btn {
    min-width: 120px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

#deleteModal .btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

#deleteModal .btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

/* Modal backdrop animation */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s ease;
}

.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

/* Alert in modal */
#deleteModal .alert {
    border-radius: 8px;
}

#deleteModal .alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c2c7;
    color: #842029;
}

/* Card hover effects */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

/* Statistics cards */
.card-body i.fa-2x {
    opacity: 0.7;
}

/* Badge spacing */
.gap-2 {
    gap: 0.5rem !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .avatar-circle {
        width: 80px !important;
        height: 80px !important;
        font-size: 28px !important;
    }
    
    #deleteModal .modal-body {
        padding: 1.5rem;
    }
    
    .delete-icon-wrapper {
        width: 60px;
        height: 60px;
    }
    
    .delete-icon-wrapper i {
        font-size: 2.5rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function showDeleteModal(username) {
    document.getElementById('deleteUsername').textContent = username;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Confirm delete button
document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    // Add loading state
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
    this.disabled = true;
    
    // Submit form
    document.getElementById('delete-form').submit();
});

// Reset when modal closed
document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus';
        confirmBtn.disabled = false;
    }
});

// Keyboard shortcut - ESC to close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (deleteModal) {
            deleteModal.hide();
        }
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(#deleteModal .alert)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush