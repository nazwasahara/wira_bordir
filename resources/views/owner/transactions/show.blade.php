@extends('layouts.owner')

@section('title', 'Detail Transaksi')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-file-alt me-2 text-warning"></i>Detail Transaksi
            <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_text }}</span>
        </h2>
        <p class="text-muted mb-0">{{ $order->order_number }}</p>
    </div>
    <div>
        <a href="{{ route('owner.transactions.history') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Riwayat
        </a>
    </div>
</div>

<!-- Transaction Stats from VIEW -->
@if(isset($orderStats))
<div class="alert alert-info border-0 shadow-sm mb-4">
    <div class="row text-center">
        <div class="col-md-3">
            <i class="fas fa-shopping-bag me-2"></i>
            <strong>{{ $orderStats->total_items }} Item</strong>
            <small class="d-block text-muted">Total Items</small>
        </div>
        <div class="col-md-3">
            <i class="fas fa-boxes me-2"></i>
            <strong>{{ $orderStats->total_quantity }} Pcs</strong>
            <small class="d-block text-muted">Total Quantity</small>
        </div>
        <div class="col-md-3">
            <i class="fas fa-credit-card me-2"></i>
            <strong class="text-{{ $orderStats->payment_status == 'LUNAS' ? 'success' : ($orderStats->payment_status == 'PARTIAL' ? 'warning' : 'danger') }}">
                {{ $orderStats->payment_status }}
            </strong>
            <small class="d-block text-muted">Status Pembayaran</small>
        </div>
        <div class="col-md-3">
            @if($orderStats->remaining_payment > 0)
                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                <strong>Rp {{ number_format($orderStats->remaining_payment, 0, ',', '.') }}</strong>
                <small class="d-block text-muted">Sisa Pembayaran</small>
            @else
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>LUNAS</strong>
                <small class="d-block text-muted">Pembayaran</small>
            @endif
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Customer Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Pelanggan</small>
                        <strong>{{ $order->customer_name }}</strong>
                        @if($order->user)
                            <br><small class="text-muted">
                                <i class="fas fa-user fa-xs"></i> User: {{ $order->user->username }}
                            </small>
                            <br><small class="text-muted">
                                <i class="fas fa-envelope fa-xs"></i> {{ $order->user->email }}
                            </small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nomor Telepon</small>
                        <strong>{{ $order->customer_phone_number }}</strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Alamat Pengiriman</small>
                        <strong>{{ $order->customer_address }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Item Pesanan ({{ $order->orderItems->count() }})</h6>
            </div>
            <div class="card-body p-0">
                @foreach($order->orderItems as $item)
                <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <!-- Item Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1 text-primary">
                                <i class="fas fa-box me-2"></i>{{ $item->product->product_name ?? 'N/A' }}
                            </h6>
                            <span class="badge bg-secondary">Quantity: {{ $item->quantity }}</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Harga per Item</small>
                            <h6 class="mb-0 text-success">{{ $item->formatted_final_price }}</h6>
                            <small class="text-muted">Subtotal: <strong class="text-primary">{{ $item->formatted_subtotal }}</strong></small>
                        </div>
                    </div>

                    <!-- Customization Details (Compact) -->
                    @if($item->material || $item->font || $item->sashType)
                    <div class="customization-compact">
                        <small class="text-muted d-block mb-2"><i class="fas fa-palette fa-xs me-1"></i>Customisasi:</small>
                        <div class="row g-2">
                            @if($item->material)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-circle fa-xs text-primary me-1"></i>{{ $item->material->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($item->materialColor)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-circle fa-xs text-info me-1"></i>{{ $item->materialColor->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($item->sashType)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-circle fa-xs text-success me-1"></i>{{ $item->sashType->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($item->font)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-circle fa-xs text-warning me-1"></i>{{ $item->font->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($item->sideMotif)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-circle fa-xs text-danger me-1"></i>{{ $item->sideMotif->name }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($item->ribbonColor)
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-circle fa-xs text-secondary me-1"></i>{{ $item->ribbonColor->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach

                <!-- Grand Total -->
                <div class="p-4 bg-light border-top">
                    <div class="row">
                        <div class="col-md-8 offset-md-4">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td class="border-0"><strong>Total Pesanan:</strong></td>
                                        <td class="border-0 text-end">
                                            <h5 class="mb-0 text-success">{{ $order->formatted_total_price }}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer History -->
        @if($customerHistory->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Transaksi Customer ({{ $customerHistory->count() }} terakhir)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Order</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customerHistory as $hist)
                            <tr>
                                <td>
                                    <small>{{ $hist->created_at->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('owner.transactions.show', $hist) }}" class="text-decoration-none">
                                        <strong>{{ $hist->order_number }}</strong>
                                    </a>
                                </td>
                                <td class="text-end">
                                    <small>{{ $hist->formatted_total_price }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $hist->status_badge_color }} badge-sm">
                                        {{ $hist->status_text }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2"><strong>Total Transaksi Customer:</strong></td>
                                <td class="text-end">
                                    <strong>Rp {{ number_format($customerHistory->sum('total_price'), 0, ',', '.') }}</strong>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Order Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pesanan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Pesanan</small>
                    <strong class="text-primary">{{ $order->order_number }}</strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $order->status_badge_color }} fs-6">
                        {{ $order->status_text }}
                    </span>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal Pesanan</small>
                    <strong>{{ $order->created_at->format('d M Y H:i') }}</strong>
                    <br><small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Terakhir Update</small>
                    <strong>{{ $order->updated_at->format('d M Y H:i') }}</strong>
                    <br><small class="text-muted">{{ $order->updated_at->diffForHumans() }}</small>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Informasi Pembayaran</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Total Harga</small>
                    <h5 class="mb-0 text-primary">{{ $order->formatted_total_price }}</h5>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Jumlah Dibayar</small>
                    @if($order->amount_paid > 0)
                        <h6 class="mb-0 text-success">{{ $order->formatted_amount_paid }}</h6>
                    @else
                        <span class="text-muted">Belum ada pembayaran</span>
                    @endif
                </div>
                
                @if($order->amount_paid > 0 && $order->amount_paid < $order->total_price)
                <div class="mb-3">
                    <small class="text-muted d-block">Sisa Pembayaran</small>
                    <h6 class="mb-0 text-warning">{{ $order->formatted_remaining_payment }}</h6>
                </div>
                @endif
                
                @if($order->payment_proof)
                <div class="mb-3">
                    <small class="text-muted d-block mb-2">Bukti Pembayaran</small>
                    <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                         alt="Bukti Pembayaran" 
                         class="img-thumbnail w-100 mb-2"
                         style="max-height: 200px; object-fit: cover; cursor: pointer;"
                         onclick="showImageModal('{{ asset('storage/' . $order->payment_proof) }}')">
                    <a href="{{ asset('storage/' . $order->payment_proof) }}" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-external-link-alt me-1"></i>Lihat Full Size
                    </a>
                </div>
                @endif
                
                @if($order->isPaymentComplete())
                <div class="alert alert-success small mb-0">
                    <i class="fas fa-check-circle me-2"></i>Pembayaran lunas
                </div>
                @else
                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Pembayaran belum lunas
                </div>
                @endif
            </div>
        </div>

        <!-- Cancelled Info -->
        @if($order->status === 'cancel' && $order->cancelledTransaction)
        <div class="card border-0 shadow-sm border-danger">
            <div class="card-header bg-danger text-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-ban me-2"></i>Informasi Pembatalan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal Dibatalkan</small>
                    <strong>{{ $order->cancelledTransaction->cancellation_date->format('d M Y H:i') }}</strong>
                </div>
                
                <div class="mb-0">
                    <small class="text-muted d-block">Alasan Pembatalan</small>
                    <p class="mb-0">{{ $order->cancelledTransaction->cancellation_reason }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img id="modalImage" src="" alt="Bukti Pembayaran" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.customization-compact {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}

.badge-sm {
    font-size: 10px;
    padding: 3px 6px;
}
</style>
@endpush

@push('scripts')
<script>
function showImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
</script>
@endpush