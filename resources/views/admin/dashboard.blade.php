@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <h2 class="mb-2 mb-md-0"><i class="fas fa-tachometer-alt me-2" style="color: #C87372;"></i>Dashboard</h2>
    <div class="text-muted desktop-only">
        <i class="fas fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
    </div>
</div>

<!-- Kartu Statistik -->
<div class="row g-3 g-md-4 mb-3 mb-md-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Pengguna</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($stats['total_users']) }}</h2>
                        <small class="opacity-75 d-none d-md-block">
                            @if($stats['users_growth'] > 0)
                                <i class="fas fa-arrow-up me-1"></i>{{ $stats['users_growth'] }}% dari bulan lalu
                            @elseif($stats['users_growth'] < 0)
                                <i class="fas fa-arrow-down me-1"></i>{{ abs($stats['users_growth']) }}% dari bulan lalu
                            @else
                                <i class="fas fa-equals me-1"></i>Tidak ada perubahan
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon d-none d-md-block">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Pesanan</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</h2>
                        <small class="opacity-75 d-none d-md-block">
                            @if($stats['orders_growth'] > 0)
                                <i class="fas fa-arrow-up me-1"></i>{{ $stats['orders_growth'] }}% dari bulan lalu
                            @elseif($stats['orders_growth'] < 0)
                                <i class="fas fa-arrow-down me-1"></i>{{ abs($stats['orders_growth']) }}% dari bulan lalu
                            @else
                                <i class="fas fa-equals me-1"></i>Tidak ada perubahan
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon d-none d-md-block">
                        <i class="fas fa-shopping-cart fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75" style="color: #191718;">Pesanan Pending</h6>
                        <h2 class="mb-0 fw-bold" style="color: #191718;">{{ number_format($stats['pending_orders']) }}</h2>
                        <small class="opacity-75 d-none d-md-block" style="color: #191718;">
                            @if($stats['pending_growth'] > 0)
                                <i class="fas fa-arrow-up me-1"></i>{{ $stats['pending_growth'] }}% dari kemarin
                            @elseif($stats['pending_growth'] < 0)
                                <i class="fas fa-arrow-down me-1"></i>{{ abs($stats['pending_growth']) }}% dari kemarin
                            @else
                                <i class="fas fa-equals me-1"></i>Tidak ada perubahan
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon d-none d-md-block">
                        <i class="fas fa-clock fa-3x" style="color: #191718;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Produk</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($stats['total_products']) }}</h2>
                        <small class="opacity-75 d-none d-md-block"><i class="fas fa-box me-1"></i>Produk Aktif</small>
                    </div>
                    <div class="stat-icon d-none d-md-block">
                        <i class="fas fa-box fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="row g-3 g-md-4 mb-3 mb-md-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Pendapatan</small>
                        <h3 class="mb-0 text-success">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
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
                        <small class="text-muted d-block">Pendapatan Bulan Ini</small>
                        <h3 class="mb-0 text-primary">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="revenue-icon bg-primary text-white">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik dan Aktivitas Terkini -->
<div class="row g-3 g-md-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Ringkasan Penjualan (12 Bulan Terakhir)</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Aktivitas Terkini</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                    <div class="list-group-item">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 small"><strong>{{ $activity['title'] }}</strong> {{ $activity['description'] }}</p>
                                <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                    <div class="list-group-item d-md-none text-center">
                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none small">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pesanan Terkini -->
<div class="row mt-3 mt-md-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Pesanan Terkini</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary d-none d-md-inline-block">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th class="d-none d-lg-table-cell">Telepon</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="d-none d-md-table-cell">Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrdersList as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->customer_name }}</td>
                                <td class="d-none d-lg-table-cell">{{ $order->customer_phone_number }}</td>
                                <td>{{ $order->formatted_total_price }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge_color }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">{{ $order->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada pesanan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3 d-md-none">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Lihat Semua Pesanan</a>
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

/* Warna putih untuk semua teks di card statistik */
.stat-card .card-body h6,
.stat-card .card-body h2,
.stat-card .card-body small,
.stat-card .card-body i {
    color: #ffffff !important;
}

/* Khusus untuk card warning (kuning) tetap putih */
.stat-card.bg-warning .card-body h6,
.stat-card.bg-warning .card-body h2,
.stat-card.bg-warning .card-body small,
.stat-card.bg-warning .card-body i {
    color: #ffffff !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Grafik Penjualan dengan Data Real
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Penjualan (dalam ribuan)',
                data: {!! json_encode($salesData) !!},
                borderColor: '#C87372',
                backgroundColor: 'rgba(200, 115, 114, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#C87372',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Rp ' + (context.parsed.y * 1000).toLocaleString('id-ID');
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(176, 197, 164, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value + 'K';
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(176, 197, 164, 0.1)'
                    }
                }
            }
        }
    });
</script>
@endpush