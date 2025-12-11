@extends('layouts.owner')

@section('title', 'Detail Log Aktivitas')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-file-alt me-2 text-success"></i>Detail Log Aktivitas
        </h2>
        <p class="text-muted mb-0">Log ID: #{{ $activityLog->id }}</p>
    </div>
    <a href="{{ route('owner.logs.admin-activity.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <!-- Log Information -->
    <div class="col-lg-8">
        <!-- Main Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Aktivitas</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Admin</small>
                        <div class="d-flex align-items-center mt-2">
                            <div class="user-avatar bg-primary text-white rounded-circle me-3"
                                 style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div>
                                <strong class="d-block">{{ $activityLog->user->username }}</strong>
                                <small class="text-muted">{{ $activityLog->user->email }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <small class="text-muted d-block">Waktu Aktivitas</small>
                        <strong class="d-block mt-2">{{ $activityLog->created_at->format('d F Y') }}</strong>
                        <small class="text-muted">{{ $activityLog->created_at->format('H:i:s') }} WIB</small>
                        <br><small class="text-muted">({{ $activityLog->created_at->diffForHumans() }})</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Jenis Aksi</small>
                        <span class="badge bg-{{ $activityLog->action_badge_color }} mt-2 fs-6">
                            <i class="fas {{ $activityLog->action_icon }} me-2"></i>
                            {{ $activityLog->action_text }}
                        </span>
                    </div>
                    
                    <div class="col-md-4">
                        <small class="text-muted d-block">Model</small>
                        <span class="badge bg-secondary mt-2 fs-6">{{ $activityLog->model }}</span>
                    </div>
                    
                    <div class="col-md-4">
                        <small class="text-muted d-block">Model ID</small>
                        <strong class="d-block mt-2">
                            {{ $activityLog->model_id ?? '-' }}
                        </strong>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-0">
                    <small class="text-muted d-block">Deskripsi Aktivitas</small>
                    <div class="alert alert-light border mt-2 mb-0">
                        <p class="mb-0">{{ $activityLog->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Changes Card (if applicable) -->
        @if($activityLog->old_values || $activityLog->new_values)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Detail Perubahan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Old Values -->
                    @if($activityLog->old_values)
                    <div class="col-md-6">
                        <div class="card bg-danger bg-opacity-10 border border-danger">
                            <div class="card-header bg-danger text-white py-2">
                                <small><i class="fas fa-times-circle me-2"></i>Nilai Sebelumnya</small>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        @foreach($activityLog->old_values as $key => $value)
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if(is_bool($value))
                                                    <span class="badge bg-{{ $value ? 'success' : 'danger' }}">
                                                        {{ $value ? 'Ya' : 'Tidak' }}
                                                    </span>
                                                @elseif(is_array($value))
                                                    <code>{{ json_encode($value) }}</code>
                                                @else
                                                    <span class="text-decoration-line-through text-danger">
                                                        {{ $value ?? '-' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- New Values -->
                    @if($activityLog->new_values)
                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10 border border-success">
                            <div class="card-header bg-success text-white py-2">
                                <small><i class="fas fa-check-circle me-2"></i>Nilai Baru</small>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        @foreach($activityLog->new_values as $key => $value)
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                            </td>
                                            <td>
                                                @if(is_bool($value))
                                                    <span class="badge bg-{{ $value ? 'success' : 'danger' }}">
                                                        {{ $value ? 'Ya' : 'Tidak' }}
                                                    </span>
                                                @elseif(is_array($value))
                                                    <code>{{ json_encode($value) }}</code>
                                                @else
                                                    <strong class="text-success">{{ $value ?? '-' }}</strong>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Information -->
    <div class="col-lg-4">
        <!-- Technical Details -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-server me-2"></i>Informasi Teknis</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">IP Address</small>
                    <strong>{{ $activityLog->ip_address ?? '-' }}</strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">User Agent</small>
                    <small class="text-break">{{ $activityLog->user_agent ?? '-' }}</small>
                </div>
                
                <div class="mb-0">
                    <small class="text-muted d-block">Log ID</small>
                    <code>#{{ $activityLog->id }}</code>
                </div>
            </div>
        </div>

        <!-- Timeline Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="timeline-marker bg-success text-white rounded-circle me-3"
                         style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Log Dibuat</small>
                        <strong>{{ $activityLog->created_at->format('d M Y H:i:s') }}</strong>
                    </div>
                </div>
                
                @if($activityLog->created_at != $activityLog->updated_at)
                <div class="d-flex align-items-center">
                    <div class="timeline-marker bg-warning text-white rounded-circle me-3"
                         style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Log Diupdate</small>
                        <strong>{{ $activityLog->updated_at->format('d M Y H:i:s') }}</strong>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Related Logs -->
        @if($activityLog->model_id)
        @php
            $relatedLogs = \App\Models\ActivityLog::where('model', $activityLog->model)
                ->where('model_id', $activityLog->model_id)
                ->where('id', '!=', $activityLog->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        
        @if($relatedLogs->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-link me-2"></i>Log Terkait ({{ $relatedLogs->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($relatedLogs as $related)
                    <a href="{{ route('owner.logs.admin-activity.show', $related->id) }}" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-{{ $related->action_badge_color }} mb-1">
                                    {{ $related->action_text }}
                                </span>
                                <br>
                                <small class="text-muted">{{ Str::limit($related->description, 40) }}</small>
                            </div>
                            <small class="text-muted">{{ $related->created_at->format('d M') }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection