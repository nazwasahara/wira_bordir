@extends('layouts.owner')

@section('title', 'Ringkasan Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Ringkasan Pesanan</h2>
        <p class="text-muted mb-0">Monitoring dan analisis pesanan pelanggan</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-inbox fa-2x text-warning mb-2"></i>
                <h6 class="text-muted mb-1">Pesanan Masuk</h6>
                <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['incoming_orders']) }}</h3>
                <small class="text-muted">Pending, Paid, Confirm</small>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h6 class="text-muted mb-1">Selesai</h6>
                <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['completed_orders']) }}</h3>
                <small class="text-muted">Pesanan selesai</small>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-cog fa-2x text-info mb-2"></i>
                <h6 class="text-muted mb-1">Diproses</h6>
                <h3 class="mb-0 fw-bold text-info">{{ number_format($stats['processing_orders']) }}</h3>
                <small class="text-muted">Sedang dikerjakan</small>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                <h6 class="text-muted mb-1">Dibatalkan</h6>
                <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['cancelled_orders']) }}</h3>
                <small class="text-muted">Order batal</small>
            </div>
        </div>
    </div>
</div>

<!-- Financial Stats -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Pendapatan (Selesai)</small>
                        <h3 class="mb-0 text-success fw-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="revenue-icon bg-success text-white">
                        <i class="fas fa-dollar-sign fa-2x"></i>
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
                        <small class="text-muted d-block">Pembayaran Pending</small>
                        <h3 class="mb-0 text-warning fw-bold">Rp {{ number_format($stats['pending_payment'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="revenue-icon bg-warning text-white">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Stats (if filtered) -->
@if($stats['date_range_stats'])
<div class="row g-3 g-md-4 mb-4">
    <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm">
            <div class="row">
                <div class="col-md-4">
                    <i class="fas fa-calendar-check me-2"></i>
                    <strong>Periode: </strong>
                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d M Y') : 'Semua' }}
                    s/d
                    {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d M Y') : 'Sekarang' }}
                </div>
                <div class="col-md-8">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong>{{ number_format($stats['date_range_stats']['total_orders']) }}</strong>
                            <small class="d-block">Total Pesanan</small>
                        </div>
                        <div class="col-4">
                            <strong>{{ number_format($stats['date_range_stats']['completed']) }}</strong>
                            <small class="d-block">Selesai</small>
                        </div>
                        <div class="col-4">
                            <strong>Rp {{ number_format($stats['date_range_stats']['total_revenue'], 0, ',', '.') }}</strong>
                            <small class="d-block">Pendapatan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.orders.summary') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama, telepon, ID..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" 
                       name="date_from" 
                       class="form-control" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" 
                       name="date_to" 
                       class="form-control" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal</option>
                    <option value="total_price" {{ request('sort_by') == 'total_price' ? 'selected' : '' }}>Total</option>
                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <label class="form-label">Urutan</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑</option>
                </select>
            </div>
            
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('owner.orders.summary') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pesanan ({{ $orders->total() }})</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th class="d-none d-md-table-cell">Telepon</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="d-none d-lg-table-cell">Tanggal</th>
                        <th class="d-none d-lg-table-cell">Pembayaran</th>
                        <th>Aksi</th> <!-- NEW -->
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong class="text-primary">{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $order->customer_name }}</strong>
                                @if($order->user)
                                    <br><small class="text-muted">
                                        <i class="fas fa-user fa-xs"></i> {{ $order->user->username }}
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <small>{{ $order->customer_phone_number }}</small>
                        </td>
                        <td>
                            <strong>{{ $order->formatted_total_price }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->status_badge_color }}">
                                {{ $order->status_text }}
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <small>
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span class="text-muted">{{ $order->created_at->format('H:i') }}</span>
                            </small>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            @if($order->amount_paid > 0)
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> 
                                    Rp {{ number_format($order->amount_paid, 0, ',', '.') }}
                                </small>
                            @else
                                <small class="text-muted">
                                    <i class="fas fa-times-circle"></i> Belum bayar
                                </small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('owner.orders.show', $order) }}" 
                               class="btn btn-sm btn-primary"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                                <span class="d-none d-md-inline ms-1">Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada pesanan ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Status Distribution Chart -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribusi Status</h6>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Ringkasan Status</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($statuses as $key => $label)
                        @php
                            $count = $stats['status_distribution'][$key] ?? 0;
                            $percentage = $stats['total_orders'] > 0 ? round(($count / $stats['total_orders']) * 100, 1) : 0;
                        @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-{{ \App\Models\Order::STATUS_PENDING === $key ? 'warning' : (\App\Models\Order::STATUS_DONE === $key ? 'success' : 'secondary') }} me-2">
                                    {{ $label }}
                                </span>
                            </div>
                            <div class="text-end">
                                <strong>{{ $count }}</strong>
                                <small class="text-muted">({{ $percentage }}%)</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.revenue-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_values($statuses)) !!},
        datasets: [{
            data: {!! json_encode(array_values($stats['status_distribution'])) !!},
            backgroundColor: [
                '#FFC107',
                '#17A2B8',
                '#007BFF',
                '#6C757D',
                '#28A745',
                '#DC3545'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.parsed || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
@endpush