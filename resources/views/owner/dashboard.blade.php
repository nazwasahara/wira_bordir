@extends('layouts.owner')

@section('title', 'Dashboard Owner')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h2 class="mb-1">Dashboard Pemilik</h2>
        <p class="text-muted mb-0">Selamat datang di panel pemilik, {{ auth()->user()->username }}</p>
    </div>
    <div class="text-muted desktop-only">
        <i class="fas fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
    </div>
</div>

<!-- REQUIREMENT #2: Main Statistics Cards -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Pesanan Masuk -->
    <div class="col-6 col-md-4">
    <div class="card border-0 shadow-sm stat-card bg-warning text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center text-white">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75 text-white">Pesanan Masuk</h6>
                    <h2 class="mb-0 fw-bold text-white">{{ number_format($stats['incoming_orders']) }}</h2>
                    <small class="opacity-75 d-none d-md-block text-white">
                        <i class="fas fa-clock me-1"></i>Pending, Paid, Confirm
                    </small>
                </div>
                <div class="stat-icon d-none d-md-block text-white">
                    <i class="fas fa-inbox fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pesanan Selesai -->
<div class="col-6 col-md-4">
    <div class="card border-0 shadow-sm stat-card bg-success text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center text-white">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75 text-white">Pesanan Selesai</h6>
                    <h2 class="mb-0 fw-bold text-white">{{ number_format($stats['completed_orders']) }}</h2>
                    <small class="opacity-75 d-none d-md-block text-white">
                        @if($stats['orders_growth'] > 0)
                            <i class="fas fa-arrow-up me-1"></i>{{ $stats['orders_growth'] }}% dari bulan lalu
                        @elseif($stats['orders_growth'] < 0)
                            <i class="fas fa-arrow-down me-1"></i>{{ abs($stats['orders_growth']) }}% dari bulan lalu
                        @else
                            <i class="fas fa-equals me-1"></i>Tidak ada perubahan
                        @endif
                    </small>
                </div>
                <div class="stat-icon d-none d-md-block text-white">
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pelanggan Aktif -->
<div class="col-6 col-md-4">
    <div class="card border-0 shadow-sm stat-card bg-primary text-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center text-white">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75 text-white">Pelanggan Aktif</h6>
                    <h2 class="mb-0 fw-bold text-white">{{ number_format($stats['active_customers']) }}</h2>
                    <small class="opacity-75 d-none d-md-block text-white">
                        @if($stats['customers_growth'] > 0)
                            <i class="fas fa-arrow-up me-1"></i>{{ $stats['customers_growth'] }}% dari bulan lalu
                        @elseif($stats['customers_growth'] < 0)
                            <i class="fas fa-arrow-down me-1"></i>{{ abs($stats['customers_growth']) }}% dari bulan lalu
                        @else
                            <i class="fas fa-equals me-1"></i>Tidak ada perubahan
                        @endif
                    </small>
                </div>
                <div class="stat-icon d-none d-md-block text-white">
                    <i class="fas fa-users fa-3x"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Additional Stats -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart fa-2x text-info mb-2"></i>
                <h6 class="text-muted mb-1">Dalam Proses</h6>
                <h4 class="mb-0 fw-bold">{{ number_format($stats['processing_orders']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                <h6 class="text-muted mb-1">Dibatalkan</h6>
                <h4 class="mb-0 fw-bold">{{ number_format($stats['cancelled_orders']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-user-shield fa-2x text-secondary mb-2"></i>
                <h6 class="text-muted mb-1">Admin Aktif</h6>
                <h4 class="mb-0 fw-bold">{{ number_format($stats['active_admins']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-box fa-2x text-warning mb-2"></i>
                <h6 class="text-muted mb-1">Produk Aktif</h6>
                <h4 class="mb-0 fw-bold">{{ number_format($stats['total_products']) }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Pendapatan</small>
                        <h3 class="mb-0 text-success fw-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="revenue-icon bg-success text-white">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Pendapatan Bulan Ini</small>
                        <h3 class="mb-0 text-primary fw-bold">Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</h3>
                        <small class="text-muted">
                            @if($stats['revenue_growth'] > 0)
                                <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $stats['revenue_growth'] }}%</span>
                            @elseif($stats['revenue_growth'] < 0)
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($stats['revenue_growth']) }}%</span>
                            @else
                                <span class="text-muted"><i class="fas fa-equals"></i> 0%</span>
                            @endif
                            dari bulan lalu
                        </small>
                    </div>
                    <div class="revenue-icon bg-primary text-white">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Rata-rata Nilai Pesanan</small>
                        <h3 class="mb-0 text-info fw-bold">Rp {{ number_format($stats['avg_order_value'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="revenue-icon bg-info text-white">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REQUIREMENT #5: Grafik Penjualan & REQUIREMENT #7: Produk Terlaris -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Sales Chart -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Grafik Penjualan (30 Hari Terakhir)</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-chart-line me-1"></i>Detail Analytics
                </a>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 5 Produk</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $index => $product)
                    <div class="list-group-item">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 small fw-bold">{{ $product['product_name'] }}</p>
                                <small class="text-muted">{{ $product['total_sold'] }} terjual</small>
                            </div>
                            <div class="text-end">
                                <small class="text-success fw-bold">Rp {{ number_format($product['revenue'], 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Belum ada data penjualan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Pesanan Terkini</h5>
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i>Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan</th>
                                <th class="d-none d-md-table-cell">Telepon</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="d-none d-lg-table-cell">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->customer_name }}</td>
                                <td class="d-none d-md-table-cell">{{ $order->customer_phone_number }}</td>
                                <td><strong>{{ $order->formatted_total_price }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge_color }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="d-none d-lg-table-cell">{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada pesanan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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

.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($salesChartData['labels']) !!},
        datasets: [{
            label: 'Penjualan (dalam ribuan)',
            data: {!! json_encode($salesChartData['data']) !!},
            borderColor: '#FFD700',
            backgroundColor: 'rgba(255, 215, 0, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#FFD700',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 3,
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
                        return 'Penjualan: Rp ' + (context.parsed.y * 1000).toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value + 'K';
                    }
                }
            }
        }
    }
});
</script>
@endpush