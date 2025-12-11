@extends('layouts.owner')

@section('title', 'Detail Pengguna')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-user me-2 text-success"></i>Detail Pengguna
            <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'success' }}">
                {{ ucfirst($user->role) }}
            </span>
            @if($user->is_active)
                <span class="badge bg-success">Aktif</span>
            @else
                <span class="badge bg-danger">Nonaktif</span>
            @endif
        </h2>
        <p class="text-muted mb-0">{{ $user->username }}</p>
    </div>
    <div>
        <a href="{{ route('owner.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- User Information -->
    <div class="col-lg-4">
        <!-- Profile Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="user-avatar-large bg-{{ $user->role == 'admin' ? 'primary' : 'success' }} text-white rounded-circle mx-auto mb-3"
                     style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                    <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                </div>
                <h4 class="mb-1">{{ $user->username }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('owner.users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit User
                    </a>
                    
                    <button type="button" 
                            class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }}"
                            data-bs-toggle="modal"
                            data-bs-target="#toggleStatusModal">
                        <i class="fas fa-{{ $user->is_active ? 'times-circle' : 'check-circle' }} me-2"></i>
                        {{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
                    </button>
                    
                    <button type="button" 
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteUserModal">
                        <i class="fas fa-trash me-2"></i>Hapus User
                    </button>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-address-card me-2"></i>Informasi Kontak</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <strong>{{ $user->email }}</strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Telepon</small>
                    <strong>{{ $user->phone_number ?? '-' }}</strong>
                </div>
                
                <div class="mb-0">
                    <small class="text-muted d-block">Alamat</small>
                    <strong>{{ $user->address ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Akun</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Role</small>
                    <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'success' }}">
                        <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }} me-1"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Status Akun</small>
                    @if($user->is_active)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> Aktif
                        </span>
                    @else
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle"></i> Nonaktif
                        </span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Email Verified</small>
                    @if($user->email_verified_at)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> Terverifikasi
                        </span>
                        <br><small class="text-muted">{{ $user->email_verified_at->format('d M Y H:i') }}</small>
                    @else
                        <span class="badge bg-warning">
                            <i class="fas fa-exclamation-triangle"></i> Belum Terverifikasi
                        </span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal Bergabung</small>
                    <strong>{{ $user->created_at->format('d M Y H:i') }}</strong>
                    <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                </div>
                
                <div class="mb-0">
                    <small class="text-muted d-block">Terakhir Update</small>
                    <strong>{{ $user->updated_at->format('d M Y H:i') }}</strong>
                    <br><small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics & Activity -->
    <div class="col-lg-8">
        <!-- Statistics Cards (Only for Customers) -->
        @if($user->role === 'customer')
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ number_format($userStats['total_orders']) }}</h4>
                        <small class="text-muted">Total Orders</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ number_format($userStats['completed_orders']) }}</h4>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ number_format($userStats['pending_orders']) }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body py-3">
                        <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                        <h4 class="mb-0 fw-bold">{{ number_format($userStats['cancelled_orders']) }}</h4>
                        <small class="text-muted">Cancelled</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Total Belanja</small>
                                <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($userStats['total_spent'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="stat-icon bg-success text-white rounded-circle"
                                 style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-dollar-sign fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Rata-rata Order</small>
                                <h4 class="mb-0 fw-bold text-info">Rp {{ number_format($userStats['avg_order_value'], 0, ',', '.') }}</h4>
                            </div>
                            <div class="stat-icon bg-info text-white rounded-circle"
                                 style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Order History (Only for Customers) -->
        @if($user->role === 'customer')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pesanan ({{ $orders->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if($orders->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Order</th>
                                <th>Tanggal</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $order->order_number }}</strong>
                                </td>
                                <td>
                                    <small>{{ $order->created_at->format('d M Y H:i') }}</small>
                                </td>
                                <td class="text-end">
                                    <strong>{{ $order->formatted_total_price }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $order->status_badge_color }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('owner.orders.show', $order) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($orders->hasPages())
                <div class="p-3 border-top">
                    {{ $orders->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada pesanan</p>
                </div>
                @endif
            </div>
        </div>
        @else
        <!-- Admin Info -->
        <div class="alert alert-info border-0 shadow-sm">
            <h5 class="alert-heading">
                <i class="fas fa-user-shield me-2"></i>Admin User
            </h5>
            <p class="mb-0">
                User ini adalah <strong>Admin</strong>. Admin memiliki akses untuk mengelola sistem,
                memproses pesanan, dan melakukan berbagai operasi administratif.
            </p>
        </div>
        @endif

        <!-- Activity Timeline -->
        @if($user->role === 'customer' && $activityTimeline->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-stream me-2"></i>Timeline Aktivitas (10 Terakhir)</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($activityTimeline as $activity)
                    <div class="timeline-item {{ !$loop->last ? 'mb-3' : '' }}">
                        <div class="timeline-marker bg-{{ $activity->status_badge_color }}">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $activity->order_number }}</strong>
                                    <span class="badge bg-{{ $activity->status_badge_color }} ms-2">
                                        {{ $activity->status_text }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $activity->formatted_total_price }}</small>
                                </div>
                                <small class="text-muted">{{ $activity->created_at->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-{{ $user->is_active ? 'warning' : 'success' }}">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="user-avatar-modal bg-{{ $user->role == 'admin' ? 'primary' : 'success' }} text-white rounded-circle mx-auto mb-3"
                         style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                    </div>
                    <h5>{{ $user->username }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
                
                <p class="text-center">
                    Apakah Anda yakin ingin <strong>{{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }}</strong> user ini?
                </p>
                
                @if(!$user->is_active)
                <div class="alert alert-info">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Setelah diaktifkan, user ini dapat login kembali ke sistem.
                    </small>
                </div>
                @else
                <div class="alert alert-warning">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Setelah dinonaktifkan, user ini tidak dapat login ke sistem.
                    </small>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('owner.users.toggle-status', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}">
                        <i class="fas fa-{{ $user->is_active ? 'times' : 'check' }} me-2"></i>
                        Ya, {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Hapus User Permanen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                    <h5>PERINGATAN!</h5>
                </div>
                
                <div class="alert alert-light border">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar bg-{{ $user->role == 'admin' ? 'primary' : 'success' }} text-white rounded-circle me-3"
                             style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                        </div>
                        <div>
                            <strong class="d-block">{{ $user->username }}</strong>
                            <small class="text-muted">{{ $user->email }}</small>
                            <br>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'success' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <p class="text-center mb-3">
                    Apakah Anda yakin ingin <strong class="text-danger">MENGHAPUS PERMANEN</strong> user ini?
                </p>
                
                <div class="alert alert-danger mb-0">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Aksi ini <strong>TIDAK DAPAT DIBATALKAN</strong></li>
                        @if($user->orders()->count() > 0)
                        <li>User ini memiliki <strong>{{ $user->orders()->count() }} transaksi</strong></li>
                        <li><strong class="text-danger">User dengan transaksi TIDAK DAPAT dihapus</strong></li>
                        <li>Gunakan fitur <strong>Nonaktifkan</strong> sebagai alternatif</li>
                        @else
                        <li>Semua data user akan terhapus dari sistem</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                @if($user->orders()->count() == 0)
                <form action="{{ route('owner.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus Permanen
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-danger" disabled title="User memiliki transaksi">
                    <i class="fas fa-ban me-2"></i>Tidak Dapat Dihapus
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 50px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -40px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 6px;
    border-left: 3px solid #dee2e6;
}

.user-avatar-large {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endpush