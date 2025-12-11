@extends('layouts.owner')

@section('title', 'Riwayat Transaksi')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Riwayat Transaksi</h2>
        <p class="text-muted mb-0">Filter dan lihat detail semua transaksi</p>
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
                <i class="fas fa-receipt fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Transaksi</h6>
                <h4 class="mb-0 fw-bold">{{ number_format($stats['total_transactions']) }}</h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-success text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-dollar-sign fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Nilai</h6>
                <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-info text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-check-circle fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Total Dibayar</h6>
                <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-gradient-warning text-white">
            <div class="card-body text-center py-3">
                <i class="fas fa-exclamation-circle fa-2x mb-2 opacity-75"></i>
                <h6 class="opacity-75 mb-1">Sisa Piutang</h6>
                <h4 class="mb-0 fw-bold">Rp {{ number_format($stats['total_unpaid'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Status Distribution -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Distribusi Status Order</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 mb-3">
                        <div class="status-box bg-warning bg-opacity-10 border border-warning rounded p-2">
                            <i class="fas fa-clock text-warning"></i>
                            <h5 class="mb-0 mt-2">{{ $stats['pending_count'] }}</h5>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="status-box bg-info bg-opacity-10 border border-info rounded p-2">
                            <i class="fas fa-credit-card text-info"></i>
                            <h5 class="mb-0 mt-2">{{ $stats['paid_count'] }}</h5>
                            <small class="text-muted">Paid</small>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="status-box bg-primary bg-opacity-10 border border-primary rounded p-2">
                            <i class="fas fa-check text-primary"></i>
                            <h5 class="mb-0 mt-2">{{ $stats['confirm_count'] }}</h5>
                            <small class="text-muted">Confirm</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-info bg-opacity-10 border border-info rounded p-2">
                            <i class="fas fa-cog text-info"></i>
                            <h5 class="mb-0 mt-2">{{ $stats['processing_count'] }}</h5>
                            <small class="text-muted">Processing</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-success bg-opacity-10 border border-success rounded p-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <h5 class="mb-0 mt-2">{{ $stats['done_count'] }}</h5>
                            <small class="text-muted">Done</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="status-box bg-danger bg-opacity-10 border border-danger rounded p-2">
                            <i class="fas fa-ban text-danger"></i>
                            <h5 class="mb-0 mt-2">{{ $stats['cancel_count'] }}</h5>
                            <small class="text-muted">Cancel</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-money-check me-2"></i>Status Pembayaran</h6>
            </div>
            <div class="card-body d-flex align-items-center">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Advanced Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Lanjutan</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.transactions.history') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama, telepon, ID, username..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status Order</label>
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
                <label class="form-label">Status Pembayaran</label>
                <select name="payment_status" class="form-select">
                    <option value="">Semua</option>
                    <option value="LUNAS" {{ request('payment_status') == 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                    <option value="PARTIAL" {{ request('payment_status') == 'PARTIAL' ? 'selected' : '' }}>Sebagian</option>
                    <option value="UNPAID" {{ request('payment_status') == 'UNPAID' ? 'selected' : '' }}>Belum Bayar</option>
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
                <a href="{{ route('owner.transactions.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Timeline Chart -->
@if($timelineData->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Timeline Transaksi</h6>
    </div>
    <div class="card-body">
        <canvas id="timelineChart" height="80"></canvas>
    </div>
</div>
@endif

<!-- Transactions Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Transaksi ({{ $transactions->total() }})</h6>
        <div>
            <small class="text-muted">Halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}</small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th>No. Order</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Dibayar</th>
                        <th class="text-center">Status Order</th>
                        <th class="text-center">Payment</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            <strong class="text-primary">{{ $transaction->order_number }}</strong>
                            @if($transaction->user)
                                <br><small class="text-muted">
                                    <i class="fas fa-user fa-xs"></i> {{ $transaction->user->username }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <small>
                                {{ $transaction->created_at->format('d M Y') }}<br>
                                <span class="text-muted">{{ $transaction->created_at->format('H:i') }}</span>
                            </small>
                        </td>
                        <td>
                            <strong>{{ $transaction->customer_name }}</strong>
                            <br><small class="text-muted">{{ $transaction->customer_phone_number }}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $transaction->total_quantity }}</span>
                        </td>
                        <td class="text-end">
                            <strong>{{ $transaction->formatted_total_price }}</strong>
                        </td>
                        <td class="text-end">
                            @if($transaction->amount_paid > 0)
                                <strong class="text-success">{{ $transaction->formatted_amount_paid }}</strong>
                                @if($transaction->remaining_payment > 0)
                                    <br><small class="text-warning">Sisa: {{ $transaction->formatted_remaining_payment }}</small>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $transaction->status_badge_color }}">
                                {{ $transaction->status_text }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($transaction->payment_status == 'LUNAS')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Lunas
                                </span>
                            @elseif($transaction->payment_status == 'PARTIAL')
                                <span class="badge bg-warning">
                                    <i class="fas fa-hourglass-half"></i> Sebagian
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i> Belum
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('owner.transactions.show', $transaction->id) }}" 
                               class="btn btn-sm btn-outline-primary"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada transaksi ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="p-3 border-top">
            {{ $transactions->links() }}
        </div>
        @endif
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
    background: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Payment Status Chart
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: ['Lunas', 'Sebagian', 'Belum Bayar'],
        datasets: [{
            data: [
                {{ $stats['lunas_count'] }},
                {{ $stats['partial_count'] }},
                {{ $stats['unpaid_count'] }}
            ],
            backgroundColor: [
                '#28A745',
                '#FFC107',
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
                        size: 12
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Timeline Chart
@if($timelineData->isNotEmpty())
const timelineCtx = document.getElementById('timelineChart').getContext('2d');
const timelineData = {!! json_encode($timelineData) !!};

const timelineLabels = timelineData.map(d => {
    const date = new Date(d.date);
    return date.getDate() + '/' + (date.getMonth() + 1);
});

const timelineOrders = timelineData.map(d => d.total_orders);
const timelineValues = timelineData.map(d => d.total_value / 1000); // Convert to thousands

new Chart(timelineCtx, {
    type: 'line',
    data: {
        labels: timelineLabels,
        datasets: [
            {
                label: 'Jumlah Transaksi',
                data: timelineOrders,
                borderColor: '#007BFF',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: false,
                yAxisID: 'y',
                pointBackgroundColor: '#007BFF',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            },
            {
                label: 'Total Nilai (dalam ribuan)',
                data: timelineValues,
                borderColor: '#28A745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1',
                pointBackgroundColor: '#28A745',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }
        ]
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
                        if (context.datasetIndex === 0) {
                            return 'Transaksi: ' + context.parsed.y;
                        } else {
                            return 'Nilai: Rp ' + (context.parsed.y * 1000).toLocaleString('id-ID');
                        }
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Jumlah Transaksi'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Nilai (K)'
                },
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value + 'K';
                    }
                }
            }
        }
    }
});
@endif
</script>
@endpush