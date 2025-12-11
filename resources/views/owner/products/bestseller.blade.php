@extends('layouts.owner')

@section('title', 'Produk Terlaris')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Produk Terlaris</h2>
        <p class="text-muted mb-0">Analisis produk dengan performa terbaik</p>
    </div>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Periode & Sorting</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.products.bestseller') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Periode</label>
                <select name="period" id="periodSelect" class="form-select">
                    <option value="7days" {{ $period == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="30days" {{ $period == '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="3months" {{ $period == '3months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                    <option value="6months" {{ $period == '6months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                    <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Sepanjang Waktu</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            
            <div class="col-md-2" id="dateFromGroup" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2" id="dateToGroup" style="display: {{ $period == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Urutkan Berdasarkan</label>
                <select name="sort_by" class="form-select">
                    <option value="revenue" {{ $sortBy == 'revenue' ? 'selected' : '' }}>Revenue (Tertinggi)</option>
                    <option value="quantity" {{ $sortBy == 'quantity' ? 'selected' : '' }}>Quantity (Terbanyak)</option>
                    <option value="orders" {{ $sortBy == 'orders' ? 'selected' : '' }}>Jumlah Order</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Terapkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-box fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Produk Terjual</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_products']) }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Revenue</h6>
                <h3 class="mb-0 fw-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-boxes fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Quantity</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_quantity']) }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Orders</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_orders']) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Performance Breakdown (ABC Analysis) -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>ABC Analysis - Kategori Performa Produk</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="performance-card bg-success bg-opacity-10 border border-success rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 text-success">Kategori A</h5>
                        <span class="badge bg-success">{{ $performanceBreakdown['category_a']['percentage'] }}%</span>
                    </div>
                    <p class="small text-muted mb-2">Produk dengan kontribusi revenue tertinggi (80% total revenue)</p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted d-block">Jumlah Produk</small>
                            <strong>{{ $performanceBreakdown['category_a']['count'] }}</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Revenue</small>
                            <strong class="text-success">Rp {{ number_format($performanceBreakdown['category_a']['revenue'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="performance-card bg-warning bg-opacity-10 border border-warning rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 text-warning">Kategori B</h5>
                        <span class="badge bg-warning">{{ $performanceBreakdown['category_b']['percentage'] }}%</span>
                    </div>
                    <p class="small text-muted mb-2">Produk dengan performa menengah (15% total revenue)</p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted d-block">Jumlah Produk</small>
                            <strong>{{ $performanceBreakdown['category_b']['count'] }}</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Revenue</small>
                            <strong class="text-warning">Rp {{ number_format($performanceBreakdown['category_b']['revenue'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="performance-card bg-danger bg-opacity-10 border border-danger rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 text-danger">Kategori C</h5>
                        <span class="badge bg-danger">{{ $performanceBreakdown['category_c']['percentage'] }}%</span>
                    </div>
                    <p class="small text-muted mb-2">Produk dengan performa rendah (5% total revenue)</p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted d-block">Jumlah Produk</small>
                            <strong>{{ $performanceBreakdown['category_c']['count'] }}</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Revenue</small>
                            <strong class="text-danger">Rp {{ number_format($performanceBreakdown['category_c']['revenue'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top 10 Products Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-crown me-2 text-warning"></i>Top 10 Produk Terlaris</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Rank</th>
                        <th>Produk</th>
                        <th class="text-center">Times Ordered</th>
                        <th class="text-center">Quantity Sold</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Avg Price</th>
                        <th class="text-center">% Kontribusi</th>
                        <th class="text-center">Period</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $product)
                    <tr>
                        <td>
                            <div class="rank-badge rank-{{ $index < 3 ? 'gold' : 'normal' }}">
                                @if($index == 0)
                                    <i class="fas fa-crown text-warning"></i> 1
                                @elseif($index == 1)
                                    <i class="fas fa-medal text-secondary"></i> 2
                                @elseif($index == 2)
                                    <i class="fas fa-award text-danger"></i> 3
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong class="d-block">{{ $product->product_name }}</strong>
                            <small class="text-muted">Base Price: Rp {{ number_format($product->product_base_price, 0, ',', '.') }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $product->times_ordered }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ number_format($product->total_quantity) }}</span>
                        </td>
                        <td class="text-end">
                            <strong class="text-success">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-end">
                            <small>Rp {{ number_format($product->avg_selling_price, 0, ',', '.') }}</small>
                        </td>
                        <td class="text-center">
                            @php
                                $contribution = $stats['total_revenue'] > 0 
                                    ? round(($product->total_revenue / $stats['total_revenue']) * 100, 1) 
                                    : 0;
                            @endphp
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $contribution > 15 ? 'success' : ($contribution > 5 ? 'warning' : 'danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $contribution }}%">
                                    {{ $contribution }}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <small>
                                {{ \Carbon\Carbon::parse($product->first_order_date)->format('d/m/Y') }}
                                <br>
                                <i class="fas fa-arrow-down fa-xs text-muted"></i>
                                <br>
                                {{ \Carbon\Carbon::parse($product->last_order_date)->format('d/m/Y') }}
                            </small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada data penjualan produk</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- All Products Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Semua Produk ({{ $bestsellerData->count() }})</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bestsellerData as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $product->product_name }}</strong></td>
                        <td class="text-center">{{ $product->times_ordered }}</td>
                        <td class="text-center">{{ number_format($product->total_quantity) }}</td>
                        <td class="text-end">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Sales Trend Chart (Top 5 Products) -->
@if($trendData->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Trend Penjualan Top 5 Produk (30 Hari Terakhir)</h6>
    </div>
    <div class="card-body">
        <canvas id="trendChart" height="80"></canvas>
    </div>
</div>
@endif

<!-- Customization Popularity -->
<div class="row g-4 mb-4">
    <!-- Material Popularity -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Material Terpopuler</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Material</th>
                                <th class="text-center">Digunakan</th>
                                <th class="text-center">Qty Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customizationStats['materials'] as $name => $data)
                            <tr>
                                <td><strong>{{ $name }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $data['count'] }} x</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $data['quantity'] }} pcs</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Font Popularity -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-font me-2"></i>Font Terpopuler</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Font</th>
                                <th class="text-center">Digunakan</th>
                                <th class="text-center">Qty Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customizationStats['fonts'] as $name => $data)
                            <tr>
                                <td><strong>{{ $name }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $data['count'] }} x</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $data['quantity'] }} pcs</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sash Type Popularity -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-tshirt me-2"></i>Tipe Sash Terpopuler</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipe Sash</th>
                                <th class="text-center">Digunakan</th>
                                <th class="text-center">Qty Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customizationStats['sash_types'] as $name => $data)
                            <tr>
                                <td><strong>{{ $name }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $data['count'] }} x</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $data['quantity'] }} pcs</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">
                                    Tidak ada data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Side Motif Popularity -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-shapes me-2"></i>Motif Samping Terpopuler</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Motif</th>
                                <th class="text-center">Digunakan</th>
                                <th class="text-center">Qty Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customizationStats['side_motifs'] as $name => $data)
                            <tr>
                                <td><strong>{{ $name }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $data['count'] }} x</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $data['quantity'] }} pcs</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">
                                    Tidak ada data
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

<!-- Insights & Recommendations -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-lightbulb me-2 text-warning"></i>Insights & Rekomendasi</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-success border-0">
                    <h6 class="alert-heading">
                        <i class="fas fa-check-circle me-2"></i>Performa Terbaik
                    </h6>
                    <ul class="mb-0 small">
                        <li>Fokus promosi pada <strong>Kategori A</strong> ({{ $performanceBreakdown['category_a']['count'] }} produk menghasilkan {{ $performanceBreakdown['category_a']['percentage'] }}% revenue)</li>
                        <li>Top produk: <strong>{{ $topProducts->first()->product_name ?? 'N/A' }}</strong> dengan {{ $topProducts->first()->times_ordered ?? 0 }} orders</li>
                        <li>Pastikan stok produk top 5 selalu tersedia</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="alert alert-warning border-0">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>Area Perbaikan
                    </h6>
                    <ul class="mb-0 small">
                        <li>Evaluasi <strong>Kategori C</strong> ({{ $performanceBreakdown['category_c']['count'] }} produk hanya {{ $performanceBreakdown['category_c']['percentage'] }}% revenue)</li>
                        <li>Pertimbangkan bundling atau promosi untuk produk kurang laku</li>
                        <li>Analisis feedback customer untuk produk dengan penjualan rendah</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info border-0 mb-0">
                    <h6 class="alert-heading">
                        <i class="fas fa-chart-line me-2"></i>Statistik Umum
                    </h6>
                    <div class="row small">
                        <div class="col-md-3">
                            <strong>Rata-rata Revenue per Produk:</strong><br>
                            Rp {{ number_format($stats['avg_revenue_per_product'], 0, ',', '.') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Produk dengan Repeat Order Tinggi:</strong><br>
                            {{ $topProducts->where('times_ordered', '>', 5)->count() }} produk
                        </div>
                        <div class="col-md-3">
                            <strong>Total Transaksi:</strong><br>
                            {{ number_format($stats['total_orders']) }} orders
                        </div>
                        <div class="col-md-3">
                            <strong>Conversion Rate:</strong><br>
                            {{ $stats['total_products'] > 0 ? round(($stats['total_orders'] / $stats['total_products']), 2) : 0 }} orders/produk
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
}

.rank-gold {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #856404;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

.rank-normal {
    background: #f8f9fa;
    color: #495057;
}

.performance-card {
    transition: transform 0.2s;
}

.performance-card:hover {
    transform: translateY(-5px);
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8f9fa;
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

// Trend Chart
@if($trendData->isNotEmpty())
const trendCtx = document.getElementById('trendChart').getContext('2d');

// Prepare data for last 30 days
const last30Days = [];
for (let i = 29; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    last30Days.push(date.toISOString().split('T')[0]);
}

const trendData = {!! json_encode($trendData) !!};
const datasets = [];
const colors = [
    { border: '#FFD700', bg: 'rgba(255, 215, 0, 0.1)' },
    { border: '#C0C0C0', bg: 'rgba(192, 192, 192, 0.1)' },
    { border: '#CD7F32', bg: 'rgba(205, 127, 50, 0.1)' },
    { border: '#007BFF', bg: 'rgba(0, 123, 255, 0.1)' },
    { border: '#28A745', bg: 'rgba(40, 167, 69, 0.1)' }
];

let colorIndex = 0;
for (const [productId, data] of Object.entries(trendData)) {
    if (colorIndex >= 5) break; // Only top 5
    
    const productName = data[0]?.product_name || 'Unknown';
    const dailyData = last30Days.map(date => {
        const found = data.find(d => d.date === date);
        return found ? found.daily_quantity : 0;
    });
    
    datasets.push({
        label: productName,
        data: dailyData,
        borderColor: colors[colorIndex].border,
        backgroundColor: colors[colorIndex].bg,
        tension: 0.4,
        fill: false,
        pointBackgroundColor: colors[colorIndex].border,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 3,
        pointHoverRadius: 5
    });
    
    colorIndex++;
}

new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: last30Days.map(date => {
            const d = new Date(date);
            return d.getDate() + '/' + (d.getMonth() + 1);
        }),
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y + ' pcs';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + ' pcs';
                    }
                }
            }
        }
    }
});
@endif
</script>
@endpush