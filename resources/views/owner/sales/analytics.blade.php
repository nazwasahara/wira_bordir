@extends('layouts.owner')

@section('title', 'Analytics Penjualan')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Analytics Penjualan</h2>
        <p class="text-muted mb-0">Dashboard lengkap performa penjualan bisnis</p>
    </div>
    <div>
        <button class="btn btn-outline-primary me-2" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
        <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Period Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Periode Analisis</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.sales.analytics') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label"><i class="fas fa-calendar me-1"></i>Periode</label>
                <select name="period" id="periodSelect" class="form-select">
                    <option value="30days" {{ $period == '30days' ? 'selected' : '' }}>30 Hari</option>
                    <option value="6months" {{ $period == '6months' ? 'selected' : '' }}>6 Bulan</option>
                    <option value="12months" {{ $period == '12months' ? 'selected' : '' }}>12 Bulan</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            
            <div class="col-md-3" id="dateFromGroup" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-3" id="dateToGroup" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Analisis
                </button>
            </div>
            
            <div class="col-md-2">
                <a href="{{ route('owner.sales.analytics') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Main Statistics Cards - 4 Columns -->
<div class="row g-3 mb-4">
    <!-- Total Revenue -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-gradient-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Pendapatan</h6>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon-circle bg-white bg-opacity-25">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
                @if($stats['revenue_growth'] != 0)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-arrow-{{ $stats['revenue_growth'] > 0 ? 'up' : 'down' }} me-1"></i>
                        <strong>{{ abs($stats['revenue_growth']) }}%</strong>
                        <small class="ms-2 opacity-75">vs periode lalu</small>
                    </div>
                @else
                    <small class="opacity-75">Tidak ada perubahan</small>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-gradient-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Pesanan</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</h3>
                    </div>
                    <div class="stat-icon-circle bg-white bg-opacity-25">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
                @if($stats['orders_growth'] != 0)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-arrow-{{ $stats['orders_growth'] > 0 ? 'up' : 'down' }} me-1"></i>
                        <strong>{{ abs($stats['orders_growth']) }}%</strong>
                        <small class="ms-2 opacity-75">vs periode lalu</small>
                    </div>
                @else
                    <small class="opacity-75">Tidak ada perubahan</small>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Average Order Value -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-gradient-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white-50 mb-1">Rata-rata Order</h6>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($stats['avg_order_value'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="stat-icon-circle bg-white bg-opacity-25">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                </div>
                <small class="opacity-75">Per transaksi selesai</small>
            </div>
        </div>
    </div>
    
    <!-- Unique Customers -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card bg-gradient-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="text-white-50 mb-1">Customer Aktif</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($stats['unique_customers']) }}</h3>
                    </div>
                    <div class="stat-icon-circle bg-white bg-opacity-25">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                @if($stats['customers_growth'] != 0)
                    <div class="d-flex align-items-center">
                        <i class="fas fa-arrow-{{ $stats['customers_growth'] > 0 ? 'up' : 'down' }} me-1"></i>
                        <strong>{{ abs($stats['customers_growth']) }}%</strong>
                        <small class="ms-2 opacity-75">vs periode lalu</small>
                    </div>
                @else
                    <small class="opacity-75">Tidak ada perubahan</small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats - 3 Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                <h6 class="text-muted mb-1">Pesanan Selesai</h6>
                <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['completed_orders']) }}</h4>
                <small class="text-muted">
                    {{ $stats['total_orders'] > 0 ? round(($stats['completed_orders'] / $stats['total_orders']) * 100, 1) : 0 }}% 
                    dari total pesanan
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-box-open fa-3x text-primary mb-2"></i>
                <h6 class="text-muted mb-1">Total Item Terjual</h6>
                <h4 class="mb-0 fw-bold text-primary">
                    @php
                        $totalItemsSold = DB::table('view_order_items_details')
                            ->join('view_order_details', 'view_order_items_details.order_id', '=', 'view_order_details.order_id')
                            ->where('view_order_details.order_status', 'done')
                            ->sum('view_order_items_details.quantity');
                    @endphp
                    {{ number_format($totalItemsSold) }}
                </h6>
                <small class="text-muted">Quantity yang terjual</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-percentage fa-3x text-warning mb-2"></i>
                <h6 class="text-muted mb-1">Conversion Rate</h6>
                <h4 class="mb-0 fw-bold text-warning">
                    {{ $stats['total_orders'] > 0 ? round(($stats['completed_orders'] / $stats['total_orders']) * 100, 1) : 0 }}%
                </h4>
                <small class="text-muted">Order completion rate</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section - 2 Main Charts Side by Side -->
<div class="row g-4 mb-4">
    <!-- Revenue Trend Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Trend Pendapatan & Pesanan</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-success active" data-chart="dual">
                            <i class="fas fa-layer-group me-1"></i>Dual
                        </button>
                        <button type="button" class="btn btn-outline-success" data-chart="revenue">
                            <i class="fas fa-dollar-sign me-1"></i>Revenue
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-chart="orders">
                            <i class="fas fa-shopping-cart me-1"></i>Orders
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Status Pesanan</h6>
            </div>
            <div class="card-body d-flex align-items-center">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="card-footer bg-white border-top">
                <div class="row text-center">
                    @foreach($salesByStatus as $status)
                        <div class="col-6 mb-2">
                            <small class="text-muted d-block">{{ ucfirst($status->status) }}</small>
                            <strong>{{ $status->total_count }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products & Monthly Trend -->
<div class="row g-4 mb-4">
    <!-- Top 10 Products -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top 10 Produk Terlaris</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Produk</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRevenueProducts = $topProducts->sum('total_revenue'); @endphp
                            @forelse($topProducts as $index => $product)
                            <tr>
                                <td>
                                    <div class="rank-badge rank-{{ $index < 3 ? 'top' : 'normal' }}">
                                        @if($index == 0)
                                            <i class="fas fa-crown text-warning"></i>
                                        @elseif($index == 1)
                                            <i class="fas fa-medal text-secondary"></i>
                                        @elseif($index == 2)
                                            <i class="fas fa-award text-danger"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong class="d-block">{{ $product->product_name }}</strong>
                                    <small class="text-muted">{{ $product->times_ordered }} orders</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ number_format($product->total_quantity) }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" 
                                             role="progressbar" 
                                             style="width: {{ $totalRevenueProducts > 0 ? ($product->total_revenue / $totalRevenueProducts) * 100 : 0 }}%">
                                            {{ $totalRevenueProducts > 0 ? round(($product->total_revenue / $totalRevenueProducts) * 100, 1) : 0 }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-2 d-block"></i>
                                    Belum ada data penjualan produk
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Comparison Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Performa Bulanan (Bar Chart)</h6>
            </div>
            <div class="card-body">
                <canvas id="monthlyBarChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Monthly Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Detail Perbandingan Bulanan</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Bulan</th>
                        <th class="text-center">Total Orders</th>
                        <th class="text-center">Selesai</th>
                        <th class="text-center">Cancel</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Avg Order</th>
                        <th class="text-center">Customers</th>
                        <th class="text-center">Growth</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyComparison as $index => $month)
                    @php
                        $prevMonth = isset($monthlyComparison[$index - 1]) ? $monthlyComparison[$index - 1] : null;
                        $growth = 0;
                        if ($prevMonth && $prevMonth->confirmed_revenue > 0) {
                            $growth = round((($month->confirmed_revenue - $prevMonth->confirmed_revenue) / $prevMonth->confirmed_revenue) * 100, 1);
                        }
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ \Carbon\Carbon::create($month->sale_year, $month->sale_month)->format('F Y') }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ number_format($month->total_orders) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success">{{ number_format($month->completed_orders) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger">{{ number_format($month->cancelled_orders) }}</span>
                        </td>
                        <td class="text-end">
                            <strong class="text-success">Rp {{ number_format($month->confirmed_revenue, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-end">
                            <small>Rp {{ number_format($month->average_order_value, 0, ',', '.') }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ number_format($month->unique_customers) }}</span>
                        </td>
                        <td class="text-center">
                            @if($growth != 0)
                                <span class="badge bg-{{ $growth > 0 ? 'success' : 'danger' }}">
                                    <i class="fas fa-arrow-{{ $growth > 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($growth) }}%
                                </span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-center">{{ number_format($monthlyComparison->sum('total_orders')) }}</th>
                        <th class="text-center">{{ number_format($monthlyComparison->sum('completed_orders')) }}</th>
                        <th class="text-center">{{ number_format($monthlyComparison->sum('cancelled_orders')) }}</th>
                        <th class="text-end">Rp {{ number_format($monthlyComparison->sum('confirmed_revenue'), 0, ',', '.') }}</th>
                        <th class="text-end">-</th>
                        <th class="text-center">-</th>
                        <th class="text-center">-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Daily Sales Detail (Accordion Style) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Detail Penjualan Harian (30 Hari Terakhir)</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#dailyTable">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
    <div class="collapse show" id="dailyTable">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Tanggal</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Selesai</th>
                            <th class="text-center">Cancel</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Paid</th>
                            <th class="text-center">Customers</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailySales as $day)
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($day->sale_date)->format('d M Y') }}</strong>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($day->sale_date)->format('l') }}</small>
                            </td>
                            <td class="text-center">{{ number_format($day->total_orders) }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ number_format($day->completed_orders) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">{{ number_format($day->cancelled_orders) }}</span>
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($day->confirmed_revenue, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end">
                                <small>Rp {{ number_format($day->total_paid, 0, ',', '.') }}</small>
                            </td>
                            <td class="text-center">{{ number_format($day->unique_customers) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data penjualan harian
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
}

.stat-card {
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rank-badge {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 14px;
}

.rank-top {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

.rank-normal {
    background: #f8f9fa;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

@media print {
    .btn, .card-header button {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Period select handler
document.getElementById('periodSelect').addEventListener('change', function() {
    const dateFromGroup = document.getElementById('dateFromGroup');
    const dateToGroup = document.getElementById('dateToGroup');
    
    if (this.value === 'custom') {
        dateFromGroup.style.display = 'block';
        dateToGroup.style.display = 'block';
    } else {
        dateFromGroup.style.display = 'none';
        dateToGroup.style.display = 'none';
    }
});

// Chart Data
const chartData = {!! json_encode($chartData) !!};

// Main Trend Chart (Dual Axis)
let trendChart;
const trendCtx = document.getElementById('trendChart').getContext('2d');

function initTrendChart(type = 'dual') {
    if (trendChart) {
        trendChart.destroy();
    }
    
    let datasets = [];
    
    if (type === 'dual' || type === 'revenue') {
        datasets.push({
            label: 'Pendapatan (dalam ribuan)',
            data: chartData.revenue,
            borderColor: '#28A745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: type === 'revenue',
            yAxisID: 'y',
            pointBackgroundColor: '#28A745',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        });
    }
    
    if (type === 'dual' || type === 'orders') {
        datasets.push({
            label: 'Jumlah Pesanan',
            data: chartData.orders,
            borderColor: '#007BFF',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: type === 'orders',
            yAxisID: type === 'dual' ? 'y1' : 'y',
            pointBackgroundColor: '#007BFF',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        });
    }
    
    const scales = type === 'dual' ? {
        y: {
            type: 'linear',
            display: true,
            position: 'left',
            title: {
                display: true,
                text: 'Revenue (K)'
            },
            ticks: {
                callback: function(value) {
                    return 'Rp ' + value + 'K';
                }
            }
        },
        y1: {
            type: 'linear',
            display: true,
            position: 'right',
            title: {
                display: true,
                text: 'Orders'
            },
            grid: {
                drawOnChartArea: false,
            },
        },
    } : {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    if (type === 'revenue') {
                        return 'Rp ' + value + 'K';
                    }
                    return value;
                }
            }
        }
    };
    
    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: scales
        }
    });
}

// Chart button handlers
document.querySelectorAll('[data-chart]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-chart]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        initTrendChart(this.dataset.chart);
    });
});

// Initialize dual chart
initTrendChart('dual');

// Status Pie Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($salesByStatus->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($salesByStatus->pluck('total_count')) !!},
            backgroundColor: [
                '#FFC107',
                '#17A2B8',
                '#007BFF',
                '#6C757D',
                '#28A745',
                '#DC3545'
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Monthly Bar Chart
const monthlyBarCtx = document.getElementById('monthlyBarChart').getContext('2d');
new Chart(monthlyBarCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyComparison->map(fn($m) => \Carbon\Carbon::create($m->sale_year, $m->sale_month)->format('M Y'))) !!},
        datasets: [
            {
                label: 'Revenue (dalam ribuan)',
                data: {!! json_encode($monthlyComparison->map(fn($m) => $m->confirmed_revenue / 1000)) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: '#28A745',
                borderWidth: 1
            },
            {
                label: 'Completed Orders',
                data: {!! json_encode($monthlyComparison->pluck('completed_orders')) !!},
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: '#007BFF',
                borderWidth: 1,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Revenue (K)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Orders'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});
</script>
@endpush