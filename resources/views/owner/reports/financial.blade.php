@extends('layouts.owner')

@section('title', 'Laporan Keuangan')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Laporan Keuangan</h2>
        <p class="text-muted mb-0">Laporan pemasukan dan pengeluaran bisnis</p>
    </div>
    <div>
        <button class="btn btn-danger me-2" onclick="printReport()">
            <i class="fas fa-print me-2"></i>Cetak Laporan
        </button>
        <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Period Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Periode Laporan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.reports.financial') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Jenis Laporan</label>
                <select name="report_type" id="reportType" class="form-select">
                    <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $reportType == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                    <option value="custom" {{ $reportType == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            
            <div class="col-md-2" id="monthGroup" style="display: {{ $reportType == 'monthly' ? 'block' : 'none' }};">
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div class="col-md-2" id="yearGroup" style="display: {{ in_array($reportType, ['monthly', 'yearly']) ? 'block' : 'none' }};">
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <div class="col-md-2" id="dateFromGroup" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2" id="dateToGroup" style="display: {{ $reportType == 'custom' ? 'block' : 'none' }};">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Report Header -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body text-center py-4">
        <h3 class="mb-2">LAPORAN KEUANGAN</h3>
        <h5 class="text-muted mb-3">{{ $report['period']['label'] }}</h5>
        <p class="text-muted mb-0">
            Periode: {{ $report['period']['start']->format('d M Y') }} - {{ $report['period']['end']->format('d M Y') }}
        </p>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <!-- Total Revenue -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <div class="stat-icon-large bg-success text-white mx-auto">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-2">PEMASUKAN (REVENUE)</h6>
                <h2 class="mb-2 fw-bold text-success">Rp {{ number_format($report['revenue']['total'], 0, ',', '.') }}</h2>
                <div class="row text-center mt-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Transaksi</small>
                        <strong>{{ number_format($report['revenue']['count']) }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Rata-rata</small>
                        <strong>Rp {{ number_format($report['revenue']['average'], 0, ',', '.') }}</strong>
                    </div>
                </div>
                @if(isset($report['comparison']['revenue']))
                <div class="mt-3 pt-3 border-top">
                    @php
                        $revenueGrowth = $report['comparison']['revenue'] > 0 
                            ? round((($report['revenue']['total'] - $report['comparison']['revenue']) / $report['comparison']['revenue']) * 100, 1) 
                            : 0;
                    @endphp
                    <small class="text-muted">vs {{ $report['comparison']['period_label'] }}</small><br>
                    <span class="badge bg-{{ $revenueGrowth >= 0 ? 'success' : 'danger' }}">
                        <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($revenueGrowth) }}%
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Total Expenses -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <div class="stat-icon-large bg-danger text-white mx-auto">
                        <i class="fas fa-arrow-down fa-2x"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-2">PENGELUARAN (EXPENSES)</h6>
                <h2 class="mb-2 fw-bold text-danger">Rp {{ number_format($report['expenses']['total'], 0, ',', '.') }}</h2>
                <div class="row text-center mt-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Invoice</small>
                        <strong>{{ number_format($report['expenses']['count']) }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Kategori</small>
                        <strong>{{ $report['expenses']['by_category']->count() }}</strong>
                    </div>
                </div>
                @if(isset($report['comparison']['expenses']))
                <div class="mt-3 pt-3 border-top">
                    @php
                        $expensesGrowth = $report['comparison']['expenses'] > 0 
                            ? round((($report['expenses']['total'] - $report['comparison']['expenses']) / $report['comparison']['expenses']) * 100, 1) 
                            : 0;
                    @endphp
                    <small class="text-muted">vs {{ $report['comparison']['period_label'] }}</small><br>
                    <span class="badge bg-{{ $expensesGrowth <= 0 ? 'success' : 'danger' }}">
                        <i class="fas fa-arrow-{{ $expensesGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($expensesGrowth) }}%
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Net Profit/Loss -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <div class="stat-icon-large bg-{{ $report['net_profit'] >= 0 ? 'primary' : 'warning' }} text-white mx-auto">
                        <i class="fas fa-{{ $report['net_profit'] >= 0 ? 'chart-line' : 'exclamation-triangle' }} fa-2x"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-2">{{ $report['net_profit'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</h6>
                <h2 class="mb-2 fw-bold text-{{ $report['net_profit'] >= 0 ? 'primary' : 'warning' }}">
                    Rp {{ number_format(abs($report['net_profit']), 0, ',', '.') }}
                </h2>
                <div class="row text-center mt-3">
                    <div class="col-12">
                        <small class="text-muted d-block">Profit Margin</small>
                        <strong class="text-{{ $report['profit_margin'] >= 0 ? 'success' : 'danger' }}">
                            {{ number_format($report['profit_margin'], 2) }}%
                        </strong>
                    </div>
                </div>
                @if(isset($report['comparison']['net_profit']))
                <div class="mt-3 pt-3 border-top">
                    @php
                        $profitGrowth = $report['comparison']['net_profit'] != 0 
                            ? round((($report['net_profit'] - $report['comparison']['net_profit']) / abs($report['comparison']['net_profit'])) * 100, 1) 
                            : 0;
                    @endphp
                    <small class="text-muted">vs {{ $report['comparison']['period_label'] }}</small><br>
                    <span class="badge bg-{{ $profitGrowth >= 0 ? 'success' : 'danger' }}">
                        <i class="fas fa-arrow-{{ $profitGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($profitGrowth) }}%
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Payment Status & Top Sources -->
<div class="row g-4 mb-4">
    <!-- Payment Status -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Status Pembayaran</h6>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-4">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h4 class="mb-0">{{ $report['payment_status']['fully_paid'] }}</h4>
                            <small class="text-muted">Lunas</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-warning bg-opacity-10 rounded">
                            <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                            <h4 class="mb-0">{{ $report['payment_status']['partial_paid'] }}</h4>
                            <small class="text-muted">Sebagian</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-danger bg-opacity-10 rounded">
                            <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                            <h4 class="mb-0">{{ $report['payment_status']['unpaid'] }}</h4>
                            <small class="text-muted">Belum Bayar</small>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Total Piutang:</strong>
                        </div>
                        <h5 class="mb-0">Rp {{ number_format($report['payment_status']['total_receivable'], 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Revenue Sources -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-star me-2"></i>Top 5 Sumber Pendapatan</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['top_sources']->take(5) as $index => $source)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $source->product_name }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $source->total_quantity }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp {{ number_format($source->total_revenue, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
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

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Revenue vs Expenses Chart -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-area me-2"></i>Grafik Pemasukan vs Pengeluaran (Harian)</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueExpensesChart" height="80"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Expenses Breakdown Pie Chart -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Breakdown Pengeluaran</h6>
            </div>
            <div class="card-body d-flex align-items-center">
                @if($report['expenses']['by_category']->isNotEmpty())
                <canvas id="expensesChart"></canvas>
                @else
                <div class="text-center w-100 py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada data pengeluaran</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detailed Revenue Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Detail Pemasukan Harian</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-center">Jumlah Order</th>
                        <th class="text-end">Total Revenue</th>
                        <th class="text-end">Cash Diterima</th>
                        <th class="text-end">Rata-rata Order</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalOrders = 0; $totalRevenue = 0; $totalPaid = 0; @endphp
                    @forelse($report['detailed_revenue'] as $day)
                        @php
                            $totalOrders += $day->orders_count;
                            $totalRevenue += $day->total_revenue;
                            $totalPaid += $day->total_paid;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</strong>
                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($day->date)->locale('id')->isoFormat('dddd') }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $day->orders_count }}</span>
                            </td>
                            <td class="text-end">
                                <strong class="text-success">Rp {{ number_format($day->total_revenue, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($day->total_paid, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end">
                                <small>Rp {{ number_format($day->avg_order_value, 0, ',', '.') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                Tidak ada data pemasukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($report['detailed_revenue']->isNotEmpty())
                <tfoot class="table-light">
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-center">{{ number_format($totalOrders) }}</th>
                        <th class="text-end">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</th>
                        <th class="text-end">Rp {{ number_format($totalPaid, 0, ',', '.') }}</th>
                        <th class="text-end">-</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Detailed Expenses Table -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-table me-2"></i>Detail Pengeluaran (Invoice)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Kategori</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($report['detailed_expenses'] as $invoiceId => $items)
                        @php
                            $invoiceTotal = $items->sum('line_total');
                            $grandTotal += $invoiceTotal;
                        @endphp
                        @foreach($items as $index => $item)
                        <tr>
                            @if($index === 0)
                            <td rowspan="{{ $items->count() }}" class="align-middle">
                                <strong class="text-primary">#INV-{{ str_pad($invoiceId, 4, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td rowspan="{{ $items->count() }}" class="align-middle">
                                {{ \Carbon\Carbon::parse($item->invoice_date)->format('d M Y') }}
                            </td>
                            @endif
                            <td>{{ $item->item_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($item->item_type) }}</span>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">
                                <strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="table-light">
                            <td colspan="6" class="text-end"><strong>Subtotal Invoice #{{ $invoiceId }}:</strong></td>
                            <td class="text-end">
                                <strong class="text-danger">Rp {{ number_format($invoiceTotal, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada data pengeluaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($report['detailed_expenses']->isNotEmpty())
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="6" class="text-end">GRAND TOTAL PENGELUARAN:</th>
                        <th class="text-end">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Summary Footer -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8 offset-md-4">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="border-0"><strong>Total Pemasukan:</strong></td>
                        <td class="border-0 text-end">
                            <h5 class="mb-0 text-success">Rp {{ number_format($report['revenue']['total'], 0, ',', '.') }}</h5>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-0"><strong>Total Pengeluaran:</strong></td>
                        <td class="border-0 text-end">
                            <h5 class="mb-0 text-danger">Rp {{ number_format($report['expenses']['total'], 0, ',', '.') }}</h5>
                        </td>
                    </tr>
                    <tr class="table-light border-top border-2">
                        <td><strong>{{ $report['net_profit'] >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}:</strong></td>
                        <td class="text-end">
                            <h4 class="mb-0 text-{{ $report['net_profit'] >= 0 ? 'primary' : 'warning' }}">
                                Rp {{ number_format(abs($report['net_profit']), 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">Profit Margin: {{ number_format($report['profit_margin'], 2) }}%</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for PDF Export -->
<form id="pdfForm" action="{{ route('owner.reports.financial.print') }}" method="GET" style="display: none;">
    <input type="hidden" name="report_type" value="{{ $reportType }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="year" value="{{ $year }}">
    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
</form>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.stat-icon-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@media print {
    .btn, .card-header button, #pdfForm {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Report Type Handler
document.getElementById('reportType').addEventListener('change', function() {
    const monthGroup = document.getElementById('monthGroup');
    const yearGroup = document.getElementById('yearGroup');
    const dateFromGroup = document.getElementById('dateFromGroup');
    const dateToGroup = document.getElementById('dateToGroup');
    
    if (this.value === 'monthly') {
        monthGroup.style.display = 'block';
        yearGroup.style.display = 'block';
        dateFromGroup.style.display = 'none';
        dateToGroup.style.display = 'none';
    } else if (this.value === 'yearly') {
        monthGroup.style.display = 'none';
        yearGroup.style.display = 'block';
        dateFromGroup.style.display = 'none';
        dateToGroup.style.display = 'none';
    } else {
        monthGroup.style.display = 'none';
        yearGroup.style.display = 'none';
        dateFromGroup.style.display = 'block';
        dateToGroup.style.display = 'block';
    }
});

// Revenue vs Expenses Chart
const revenueData = {!! json_encode($report['detailed_revenue']->pluck('total_revenue')->toArray()) !!};
const expensesDataArray = {!! json_encode($report['detailed_revenue']->map(function($day) use ($report) {
    $date = \Carbon\Carbon::parse($day->date)->format('Y-m-d');
    $invoicesOnDate = $report['detailed_expenses']->flatten(1)->filter(function($expense) use ($date) {
        return \Carbon\Carbon::parse($expense->invoice_date)->format('Y-m-d') === $date;
    });
    return $invoicesOnDate->sum('line_total');
})->toArray()) !!};
const labels = {!! json_encode($report['detailed_revenue']->map(fn($d) => \Carbon\Carbon::parse($d->date)->format('d M'))->toArray()) !!};

const revenueExpensesCtx = document.getElementById('revenueExpensesChart').getContext('2d');
new Chart(revenueExpensesCtx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Pemasukan',
                data: revenueData,
                borderColor: '#28A745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#28A745',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            },
            {
                label: 'Pengeluaran',
                data: expensesDataArray,
                borderColor: '#DC3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#DC3545',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000) + 'K';
                    }
                }
            }
        }
    }
});

// Expenses Breakdown Pie Chart
@if($report['expenses']['by_category']->isNotEmpty())
const expensesCtx = document.getElementById('expensesChart').getContext('2d');
new Chart(expensesCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($report['expenses']['by_category']->keys()->map(fn($k) => ucfirst($k))) !!},
        datasets: [{
            data: {!! json_encode($report['expenses']['by_category']->pluck('total')->toArray()) !!},
            backgroundColor: [
                '#007BFF',
                '#28A745',
                '#FFC107',
                '#DC3545',
                '#17A2B8',
                '#6C757D'
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
                    padding: 15
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID') + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
@endif

// Print Report Function
function printReport() {
    const reportType = '{{ $reportType }}';
    const month = '{{ $month }}';
    const year = '{{ $year }}';
    const dateFrom = '{{ request("date_from") }}';
    const dateTo = '{{ request("date_to") }}';
    
    let url = '{{ route("owner.reports.financial.print") }}';
    url += '?report_type=' + reportType;
    url += '&month=' + month;
    url += '&year=' + year;
    
    if (dateFrom) url += '&date_from=' + dateFrom;
    if (dateTo) url += '&date_to=' + dateTo;
    
    window.open(url, '_blank');
}
</script>
@endpush