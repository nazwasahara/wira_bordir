@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Manajemen Pesanan</h2>
    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Buat Pesanan Baru
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total</h6>
                <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Pending</h6>
                <h4 class="mb-0 fw-bold text-warning">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Dikonfirmasi</h6>
                <h4 class="mb-0 fw-bold text-info">{{ $stats['confirmed'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Proses</h6>
                <h4 class="mb-0 fw-bold text-primary">{{ $stats['processing'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Selesai</h6>
                <h4 class="mb-0 fw-bold text-success">{{ $stats['completed'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Dibatalkan</h6>
                <h4 class="mb-0 fw-bold text-danger">{{ $stats['cancelled'] }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
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
    <div class="col-md-4">
        <div class="card border-0 shadow-sm border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">
                            <i class="fas fa-database me-1 text-primary"></i>
                            Revenue Bulan Ini
                        </small>
                        <h3 class="mb-0 text-primary">Rp {{ number_format($stats['revenue_this_month'] ?? 0, 0, ',', '.') }}</h3>
                        <small class="text-muted">
                            {{ now()->format('F Y') }}
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
                        <small class="text-muted d-block">Pending Pembayaran</small>
                        <h3 class="mb-0 text-warning">Rp {{ number_format($stats['pending_payment'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="revenue-icon bg-warning text-white">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari nama, telepon, atau ID pesanan..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Order::getStatuses() as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
            <div class="col-md-12">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo me-2"></i>Reset Filter
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Daftar Pesanan</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="100">No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Dibayar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong class="text-primary">{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            <strong>{{ $order->customer_name }}</strong><br>
                            <small class="text-muted">{{ $order->customer_phone_number }}</small>
                        </td>
                        <td>
                            <strong class="text-success">{{ $order->formatted_total_price }}</strong>
                        </td>
                        <td>
                            <strong>{{ $order->formatted_amount_paid }}</strong>
                            @if(!$order->isPaymentComplete())
                                <br><small class="text-danger">Kurang: {{ $order->formatted_remaining_payment }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $order->status_badge_color }}">
                                {{ $order->status_text }}
                            </span>
                        </td>
                        <td>
                            {{ $order->created_at->format('d M Y') }}<br>
                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                        </td>
                        <td>
													<div class="btn-group" role="group">
															<a href="{{ route('admin.orders.show', $order) }}" 
																class="btn btn-sm btn-outline-primary"
																title="Detail">
																	<i class="fas fa-eye"></i>
															</a>
															
															@if(!in_array($order->status, [\App\Models\Order::STATUS_PROCESSING, \App\Models\Order::STATUS_DONE]))
																	<a href="{{ route('admin.orders.edit', $order) }}" 
																		class="btn btn-sm btn-outline-secondary"
																		title="Edit">
																			<i class="fas fa-edit"></i>
																	</a>
															@endif
															
															@if($order->status === \App\Models\Order::STATUS_CANCEL)
																	<button type="button"
																					class="btn btn-sm btn-outline-danger"
																					onclick="showDeleteModal({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->customer_name }}')"
																					title="Hapus">
																			<i class="fas fa-trash"></i>
																	</button>
																	<form id="delete-form-{{ $order->id }}" 
																				action="{{ route('admin.orders.destroy', $order) }}" 
																				method="POST" 
																				class="d-none">
																			@csrf
																			@method('DELETE')
																	</form>
															@endif
													</div>
											</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada pesanan ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $orders->firstItem() }} sampai {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
            </div>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Order Modal -->
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                    </div>
                </div>
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Pesanan</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus pesanan:</p>
                <p class="fw-bold text-primary mb-1" id="deleteOrderNumber"></p>
                <p class="text-muted small mb-3">Pelanggan: <strong id="deleteCustomerName"></strong></p>
                <div class="alert alert-danger d-flex align-items-start mb-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div class="text-start">
                        <small class="d-block mb-1"><strong>Perhatian!</strong></small>
                        <small>
                            • Semua item pesanan akan dihapus<br>
                            • Tindakan ini tidak dapat dibatalkan!
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus Pesanan
                </button>
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

.delete-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
    }
    70% {
        box-shadow: 0 0 0 20px rgba(255, 193, 7, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

.modal-content {
    border-radius: 16px;
}
</style>
@endpush

@push('scripts')
<script>
let deleteOrderId = null;

function showDeleteModal(orderId, orderNumber, customerName) {
    deleteOrderId = orderId;
    document.getElementById('deleteOrderNumber').textContent = orderNumber;
    document.getElementById('deleteCustomerName').textContent = customerName;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    if (deleteOrderId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        document.getElementById('delete-form-' + deleteOrderId).submit();
    }
});

// Reset modal when closed
document.getElementById('deleteOrderModal')?.addEventListener('hidden.bs.modal', function() {
    deleteOrderId = null;
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus Pesanan';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush