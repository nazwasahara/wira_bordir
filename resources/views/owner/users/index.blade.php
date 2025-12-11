@extends('layouts.owner')

@section('title', 'Manajemen Pengguna')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Manajemen Pengguna</h2>
        <p class="text-muted mb-0">Kelola Admin dan Customer</p>
    </div>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Total Users -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-users fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Pengguna</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h3>
                <small class="opacity-75">Admin + Customer</small>
            </div>
        </div>
    </div>
    
    <!-- Active Admins -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-info text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-user-shield fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Admin Aktif</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['active_admins']) }}</h3>
                <small class="opacity-75">dari {{ $stats['total_admins'] }} admin</small>
            </div>
        </div>
    </div>
    
    <!-- Active Customers -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-success text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-user-friends fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Customer Aktif</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['active_customers']) }}</h3>
                <small class="opacity-75">dari {{ $stats['total_customers'] }} customer</small>
            </div>
        </div>
    </div>
    
    <!-- New Users This Month -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-user-plus fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">User Baru Bulan Ini</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['new_users_this_month']) }}</h3>
                <small class="opacity-75">{{ \Carbon\Carbon::now()->format('F Y') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Status Overview -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user-shield me-2"></i>Status Admin</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="status-box bg-primary bg-opacity-10 border border-primary rounded p-3">
                            <h4 class="mb-0 text-primary">{{ $stats['total_admins'] }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-success bg-opacity-10 border border-success rounded p-3">
                            <h4 class="mb-0 text-success">{{ $stats['active_admins'] }}</h4>
                            <small class="text-muted">Aktif</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-danger bg-opacity-10 border border-danger rounded p-3">
                            <h4 class="mb-0 text-danger">{{ $stats['inactive_admins'] }}</h4>
                            <small class="text-muted">Nonaktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user-friends me-2"></i>Status Customer</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="status-box bg-primary bg-opacity-10 border border-primary rounded p-3">
                            <h4 class="mb-0 text-primary">{{ $stats['total_customers'] }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-success bg-opacity-10 border border-success rounded p-3">
                            <h4 class="mb-0 text-success">{{ $stats['active_customers'] }}</h4>
                            <small class="text-muted">Aktif</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-danger bg-opacity-10 border border-danger rounded p-3">
                            <h4 class="mb-0 text-danger">{{ $stats['inactive_customers'] }}</h4>
                            <small class="text-muted">Nonaktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Pengguna</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.users.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Username, email, telepon..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Daftar</option>
                    <option value="username" {{ request('sort_by') == 'username' ? 'selected' : '' }}>Username</option>
                    <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <label class="form-label">Urutan</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
            </div>
        </form>
        
        @if(request()->hasAny(['search', 'role', 'status', 'sort_by']))
        <div class="mt-3">
            <a href="{{ route('owner.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-redo me-1"></i>Reset Filter
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pengguna ({{ $users->total() }})</h6>
        <small class="text-muted">Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th width="50">#</th>
                        <th>User Info</th>
                        <th>Kontak</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Bergabung</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>
                            <strong>{{ $users->firstItem() + $index }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar bg-{{ $user->role == 'admin' ? 'primary' : 'success' }} text-white rounded-circle me-3"
                                     style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                                </div>
                                <div>
                                    <strong class="d-block">{{ $user->username }}</strong>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small>
                                <i class="fas fa-phone fa-xs text-muted me-1"></i>{{ $user->phone_number ?? '-' }}<br>
                                @if($user->address)
                                    <i class="fas fa-map-marker-alt fa-xs text-muted me-1"></i>{{ Str::limit($user->address, 30) }}
                                @endif
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'success' }}">
                                <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }} me-1"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($user->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <small>
                                {{ $user->created_at->format('d M Y') }}<br>
                                <span class="text-muted">{{ $user->created_at->diffForHumans() }}</span>
                            </small>
                        </td>
                        <td>
                                      <div class="btn-group btn-group-sm" role="group">
                      <a href="{{ route('owner.users.show', $user) }}" 
                        class="btn btn-outline-primary"
                        title="Detail">
                          <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('owner.users.edit', $user) }}" 
                        class="btn btn-outline-warning"
                        title="Edit">
                          <i class="fas fa-edit"></i>
                      </a>
                      <button type="button"
                              class="btn btn-outline-{{ $user->is_active ? 'secondary' : 'success' }}"
                              data-bs-toggle="modal"
                              data-bs-target="#toggleModal{{ $user->id }}"
                              title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                          <i class="fas fa-{{ $user->is_active ? 'times' : 'check' }}"></i>
                      </button>
                      <button type="button"
                              class="btn btn-outline-danger"
                              data-bs-toggle="modal"
                              data-bs-target="#deleteModal{{ $user->id }}"
                              title="Hapus">
                          <i class="fas fa-trash"></i>
                      </button>
                  </div>
              </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-users fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada pengguna ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="p-3 border-top">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Top Customers (if available) -->
@if($customerStats->isNotEmpty())
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-star me-2 text-warning"></i>Top 10 Customer (Berdasarkan Total Belanja)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Customer</th>
                        <th class="text-center">Total Orders</th>
                        <th class="text-center">Completed</th>
                        <th class="text-end">Total Spent</th>
                        <th class="text-end">Avg Order</th>
                        <th class="text-center">Last Order</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerStats as $index => $customer)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $customer->username }}</strong><br>
                            <small class="text-muted">{{ $customer->email }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $customer->total_orders }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ $customer->completed_orders }}</span>
                        </td>
                        <td class="text-end">
                            <strong class="text-success">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-end">
                            <small>Rp {{ number_format($customer->average_order_value, 0, ',', '.') }}</small>
                        </td>
                        <td class="text-center">
                            <small>
                                @if($customer->last_order_date)
                                    {{ \Carbon\Carbon::parse($customer->last_order_date)->format('d M Y') }}<br>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($customer->last_order_date)->diffForHumans() }}</span>
                                @else
                                    -
                                @endif
                            </small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
<!-- Toggle Status Modals -->
@foreach($users as $user)
<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
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
                <div class="text-center mb-3">
                    <i class="fas fa-user-{{ $user->is_active ? 'slash' : 'check' }} fa-4x text-{{ $user->is_active ? 'warning' : 'success' }}"></i>
                </div>
                <p class="text-center mb-0">
                    Apakah Anda yakin ingin <strong>{{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }}</strong> user:
                </p>
                <div class="alert alert-light border mt-3 mb-0">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar bg-{{ $user->role == 'admin' ? 'primary' : 'success' }} text-white rounded-circle me-3"
                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                        </div>
                        <div>
                            <strong class="d-block">{{ $user->username }}</strong>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                </div>
                @if(!$user->is_active)
                <div class="alert alert-info mt-3 mb-0">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Setelah diaktifkan, user ini dapat login kembali ke sistem.
                    </small>
                </div>
                @else
                <div class="alert alert-warning mt-3 mb-0">
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Hapus User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                </div>
                <p class="text-center mb-0">
                    Apakah Anda yakin ingin <strong class="text-danger">MENGHAPUS PERMANEN</strong> user ini?
                </p>
                <div class="alert alert-light border mt-3 mb-0">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar bg-{{ $user->role == 'admin' ? 'primary' : 'success' }} text-white rounded-circle me-3"
                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-{{ $user->role == 'admin' ? 'user-shield' : 'user' }}"></i>
                        </div>
                        <div>
                            <strong class="d-block">{{ $user->username }}</strong>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                </div>
                <div class="alert alert-danger mt-3 mb-0">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>PERINGATAN:</strong> Aksi ini tidak dapat dibatalkan! 
                        @if($user->orders()->count() > 0)
                        <br>User ini memiliki <strong>{{ $user->orders()->count() }} transaksi</strong> dan tidak dapat dihapus.
                        @endif
                    </small>
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
                <button type="button" class="btn btn-danger" disabled>
                    <i class="fas fa-ban me-2"></i>Tidak Dapat Dihapus
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
.user-avatar {
    font-size: 1.2rem;
}

.status-box {
    transition: transform 0.2s;
}

.status-box:hover {
    transform: translateY(-3px);
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa;
}
</style>
@endpush