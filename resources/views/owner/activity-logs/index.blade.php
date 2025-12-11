@extends('layouts.owner')

@section('title', 'Log Aktivitas Admin')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Log Aktivitas Admin</h2>
        <p class="text-muted mb-0">Pantau semua aktivitas admin di sistem</p>
    </div>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-list fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Log</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_logs']) }}</h3>
                <small class="opacity-75">Semua aktivitas</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-success text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-calendar-day fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Log Hari Ini</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['today_logs']) }}</h3>
                <small class="opacity-75">{{ \Carbon\Carbon::now()->format('d M Y') }}</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-info text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-user-shield fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Admin Aktif</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['active_admins']) }}</h3>
                <small class="opacity-75">Yang dapat login</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-chart-bar fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Jenis Aktivitas</h6>
                <h3 class="mb-0 fw-bold">{{ $stats['actions_breakdown']->count() }}</h3>
                <small class="opacity-75">Kategori aksi</small>
            </div>
        </div>
    </div>
</div>

<!-- Action Breakdown -->
@if($stats['actions_breakdown']->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Breakdown Aktivitas</h6>
    </div>
    <div class="card-body">
        <div class="row text-center">
            @foreach($stats['actions_breakdown'] as $action => $count)
            <div class="col-md-2 col-6 mb-3">
                <div class="p-3 border rounded">
                    <i class="fas {{ match($action) {
                        'create' => 'fa-plus text-success',
                        'update' => 'fa-edit text-warning',
                        'delete' => 'fa-trash text-danger',
                        'status_change' => 'fa-exchange-alt text-info',
                        'login' => 'fa-sign-in-alt text-primary',
                        default => 'fa-info-circle text-secondary'
                    } }} fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ number_format($count) }}</h4>
                    <small class="text-muted">{{ ucfirst($action) }}</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Advanced Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Lanjutan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.logs.admin-activity.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Deskripsi, model, username..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Admin</label>
                <select name="admin_id" class="form-select">
                    <option value="">Semua Admin</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                            {{ $admin->username }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                            {{ ucfirst($act) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Model</label>
                <select name="model" class="form-select">
                    <option value="">Semua Model</option>
                    @foreach($models as $mdl)
                        <option value="{{ $mdl }}" {{ request('model') == $mdl ? 'selected' : '' }}>
                            {{ $mdl }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Periode</label>
                <div class="input-group">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Sampai">
                </div>
            </div>
            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('owner.logs.admin-activity.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Activity Logs Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Aktivitas ({{ $logs->total() }})</h6>
        <small class="text-muted">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th width="50">#</th>
                        <th>Waktu</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                        <th>Model</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>
                            <small>
                                <strong>{{ $log->created_at->format('d M Y') }}</strong><br>
                                {{ $log->created_at->format('H:i:s') }}<br>
                                <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <strong>{{ $log->user->username }}</strong><br>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $log->action_badge_color }}">
                                <i class="fas {{ $log->action_icon }} me-1"></i>
                                {{ $log->action_text }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $log->model }}</span>
                        </td>
                        <td>
                            <small>{{ Str::limit($log->description, 80) }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('owner.logs.admin-activity.show', $log) }}" 
                               class="btn btn-sm btn-outline-primary"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada log aktivitas ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="p-3 border-top">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa;
}

.user-avatar {
    flex-shrink: 0;
}
</style>
@endpush
