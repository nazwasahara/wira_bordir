@extends('layouts.owner')

@section('title', 'Detail Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-file-invoice me-2 text-warning"></i>Detail Pesanan
            <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_text }}</span>
        </h2>
        <p class="text-muted mb-0">{{ $order->order_number }}</p>
    </div>
    <a href="{{ route('owner.orders.summary') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Additional Order Stats from VIEW (if available) -->
@if(isset($orderStats))
<div class="alert alert-info border-0 shadow-sm mb-4">
    <div class="row text-center">
        <div class="col-md-3">
            <i class="fas fa-shopping-bag me-2"></i>
            <strong>{{ $orderStats->total_items }} Item</strong>
            <small class="d-block">Total Items</small>
        </div>
        <div class="col-md-3">
            <i class="fas fa-boxes me-2"></i>
            <strong>{{ $orderStats->total_quantity }} Pcs</strong>
            <small class="d-block">Total Quantity</small>
        </div>
        <div class="col-md-3">
            <i class="fas fa-credit-card me-2"></i>
            <strong class="text-{{ $orderStats->payment_status == 'LUNAS' ? 'success' : ($orderStats->payment_status == 'PARTIAL' ? 'warning' : 'danger') }}">
                {{ $orderStats->payment_status }}
            </strong>
            <small class="d-block">Status Pembayaran</small>
        </div>
        <div class="col-md-3">
            @if($orderStats->remaining_payment > 0)
                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                <strong>Rp {{ number_format($orderStats->remaining_payment, 0, ',', '.') }}</strong>
                <small class="d-block">Sisa Pembayaran</small>
            @else
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>LUNAS</strong>
                <small class="d-block">Pembayaran</small>
            @endif
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Order Information -->
    <div class="col-lg-8">
        <!-- Customer Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Pelanggan</small>
                        <strong>{{ $order->customer_name }}</strong>
                        @if($order->user)
                            <br><small class="text-muted">
                                <i class="fas fa-user fa-xs"></i> User: {{ $order->user->username }} ({{ $order->user->email }})
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
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Item Pesanan ({{ $order->orderItems->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @foreach($order->orderItems as $item)
                <div class="order-item-card {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="p-4">
                        <!-- Item Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1 text-primary">
                                    <i class="fas fa-box me-2"></i>{{ $item->product->product_name ?? 'N/A' }}
                                </h5>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Harga per Item</small>
                                <h5 class="mb-0 text-success">{{ $item->formatted_final_price }}</h5>
                                <small class="text-muted">Subtotal: <strong class="text-primary">{{ $item->formatted_subtotal }}</strong></small>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-header bg-white py-2">
                                <h6 class="mb-0 small">
                                    <i class="fas fa-calculator me-2"></i>Rincian Harga per Item
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                @foreach($item->price_breakdown as $index => $breakdown)
                                                    @if($index < ceil(count($item->price_breakdown) / 2))
                                                    <tr>
                                                        <td class="text-muted small">{{ $breakdown['label'] }}</td>
                                                        <td class="text-end small">
                                                            <strong>Rp {{ number_format($breakdown['price'], 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                @foreach($item->price_breakdown as $index => $breakdown)
                                                    @if($index >= ceil(count($item->price_breakdown) / 2))
                                                    <tr>
                                                        <td class="text-muted small">{{ $breakdown['label'] }}</td>
                                                        <td class="text-end small">
                                                            <strong>Rp {{ number_format($breakdown['price'], 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Total Calculation -->
                                <div class="border-top mt-2 pt-2">
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Total Harga per Item:</strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <strong class="text-success">{{ $item->formatted_calculated_price }}</strong>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <span>Quantity:</span>
                                        </div>
                                        <div class="col-6 text-end">
                                            <strong>× {{ $item->quantity }}</strong>
                                        </div>
                                    </div>
                                    <div class="row border-top mt-2 pt-2">
                                        <div class="col-6">
                                            <strong class="text-primary">Subtotal:</strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <h5 class="mb-0 text-primary">{{ $item->formatted_subtotal }}</h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Verification -->
                                @if(abs($item->calculated_price - $item->final_price) > 0.01)
                                <div class="alert alert-warning small mb-0 mt-2" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Harga yang tersimpan ({{ $item->formatted_final_price }}) 
                                    berbeda dengan kalkulasi ({{ $item->formatted_calculated_price }})
                                </div>
                                @else
                                <div class="alert alert-success small mb-0 mt-2" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Harga sudah sesuai dengan kalkulasi
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Customization Details -->
                        <div class="customization-details">
                            <h6 class="small text-muted mb-2">
                                <i class="fas fa-palette me-2"></i>Detail Customisasi
                            </h6>
                            <div class="row g-2">
                                @if($item->material)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-primary me-1"></i>
                                            <strong>Material:</strong> {{ $item->material->name }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->materialColor)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-info me-1"></i>
                                            <strong>Warna:</strong> {{ $item->materialColor->name }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->sashType)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-success me-1"></i>
                                            <strong>Tipe Sash:</strong> {{ $item->sashType->name }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->font)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-warning me-1"></i>
                                            <strong>Font:</strong> {{ $item->font->name }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->sideMotif)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-danger me-1"></i>
                                            <strong>Motif:</strong> {{ $item->sideMotif->name }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->ribbonColor)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-secondary me-1"></i>
                                            <strong>Pita:</strong> {{ $item->ribbonColor->name }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->laceOption)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-primary me-1"></i>
                                            <strong>Renda:</strong> {{ $item->laceOption->color }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->rombeOption)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-info me-1"></i>
                                            <strong>Rombe:</strong> {{ $item->rombeOption->color }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->motifRibbonOption)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-success me-1"></i>
                                            <strong>Pita Motif:</strong> {{ $item->motifRibbonOption->color }}
                                        </div>
                                    </div>
                                @endif
                                
                                @if($item->additionalItemOption)
                                    <div class="col-md-3">
                                        <div class="custom-badge">
                                            <i class="fas fa-circle fa-xs text-warning me-1"></i>
                                            <strong>Item+:</strong> {{ $item->additionalItemOption->additionalItem->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Text and Logo Information -->
                        @if($item->text_right || $item->text_left || $item->text_single || $item->logo_path)
                        <div class="text-logo-info mt-3 pt-3 border-top">
                            <h6 class="small text-muted mb-3">
                                <i class="fas fa-font me-2"></i>Informasi Teks & Logo
                            </h6>
                            <div class="row g-3">
                                @if($item->text_right)
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-align-right me-1"></i>Teks Kanan
                                            </small>
                                            <strong class="d-block">{{ $item->text_right }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($item->text_left)
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-align-left me-1"></i>Teks Kiri
                                            </small>
                                            <strong class="d-block">{{ $item->text_left }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($item->text_single)
                                <div class="col-md-12">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-align-center me-1"></i>Teks Tunggal
                                            </small>
                                            <strong class="d-block">{{ $item->text_single }}</strong>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($item->logo_path)
                                <div class="col-md-12">
                                    <div class="card bg-light border-0">
                                        <div class="card-body p-3">
                                            <small class="text-muted d-block mb-2">
                                                <i class="fas fa-image me-1"></i>Logo
                                            </small>
                                            <div class="text-center">
                                                <img src="{{ asset('storage/' . $item->logo_path) }}" 
                                                     alt="Logo" 
                                                     class="img-thumbnail"
                                                     style="max-width: 200px; max-height: 200px; object-fit: contain; cursor: pointer;"
                                                     onclick="showImageModal('{{ asset('storage/' . $item->logo_path) }}')">
                                                <div class="mt-2">
                                                    <a href="{{ asset('storage/' . $item->logo_path) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt me-1"></i>Lihat Full Size
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Grand Total -->
                <div class="p-4 bg-light border-top">
                    <div class="row">
                        <div class="col-md-8 offset-md-4">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td><strong>Total Semua Item:</strong></td>
                                        <td class="text-end">
                                            <h4 class="mb-0 text-success">{{ $order->formatted_total_price }}</h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary Sidebar -->
    <div class="col-lg-4">
        <!-- Order Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
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
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Informasi Pembayaran</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Total Harga</small>
                    <h4 class="mb-0 text-primary">{{ $order->formatted_total_price }}</h4>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Jumlah Dibayar</small>
                    @if($order->amount_paid > 0)
                        <h5 class="mb-0 text-success">{{ $order->formatted_amount_paid }}</h5>
                    @else
                        <span class="text-muted">Belum ada pembayaran</span>
                    @endif
                </div>
                
                @if($order->amount_paid > 0 && $order->amount_paid < $order->total_price)
                <div class="mb-3">
                    <small class="text-muted d-block">Sisa Pembayaran</small>
                    <h5 class="mb-0 text-warning">{{ $order->formatted_remaining_payment }}</h5>
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
        <div class="card border-0 shadow-sm border-danger mb-4">
            <div class="card-header bg-danger text-white">
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
.order-item-card {
    transition: all 0.3s ease;
}

.custom-badge {
    background: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    border: 1px solid #e0e0e0;
}

.customization-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px dashed #dee2e6;
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