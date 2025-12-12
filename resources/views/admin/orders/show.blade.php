@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-shopping-cart me-2"></i>Detail Pesanan
        <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_text }}</span>
    </h2>
    <div>
        <form action="{{ route('admin.orders.recalculate', $order) }}" method="POST" class="d-inline me-2">
            @csrf
            <button type="submit" class="btn btn-outline-primary" title="Hitung ulang total menggunakan SQL function">
                <i class="fas fa-calculator me-2"></i>Hitung Ulang Total
            </button>
        </form>
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-info me-2" target="_blank">
            <i class="fas fa-print me-2"></i>Cetak Invoice
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Order Summary Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pesanan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Pesanan</small>
                    <strong class="h5 text-primary">{{ $order->order_number }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal Pesanan</small>
                    <strong>{{ $order->created_at->format('d M Y, H:i') }}</strong>
                    <small class="d-block text-muted">{{ $order->created_at->diffForHumans() }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $order->status_badge_color }} fs-6">{{ $order->status_text }}</span>
                </div>
                <hr>
                <div class="mb-3">
                    <small class="text-muted d-block">Total Pesanan (Tersimpan)</small>
                    <strong class="h4 text-success">{{ $order->formatted_total_price }}</strong>
                </div>
                @if(isset($calculatedTotal))
                <div class="mb-3">
                    <div class="card bg-light border-primary">
                        <div class="card-body p-3">
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-database me-1 text-primary"></i>
                                <strong>Total</strong>
                            </small>
                            <strong class="h5 text-primary mb-2 d-block">Rp {{ number_format($calculatedTotal, 0, ',', '.') }}</strong>
                            @if(abs($calculatedTotal - $order->total_price) > 0.01)
                                <div class="alert alert-warning small mb-0 mt-2" role="alert">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Perbedaan ditemukan!</strong><br>
                                    <small>
                                        Tersimpan: Rp {{ number_format($order->total_price, 0, ',', '.') }}<br>
                                        Dihitung: Rp {{ number_format($calculatedTotal, 0, ',', '.') }}<br>
                                        Selisih: Rp {{ number_format(abs($calculatedTotal - $order->total_price), 0, ',', '.') }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <div class="mb-3">
                    <small class="text-muted d-block">Jumlah Dibayar</small>
                    <strong class="h5">{{ $order->formatted_amount_paid }}</strong>
                </div>
                @if(!$order->isPaymentComplete())
                <div class="mb-3">
                    <small class="text-muted d-block">Sisa Pembayaran</small>
                    <strong class="h5 text-danger">{{ $order->formatted_remaining_payment }}</strong>
                </div>
                @else
                <div class="alert alert-success small mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>Pembayaran Lunas
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Nama</small>
                    <strong>{{ $order->customer_name }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Telepon</small>
                    <strong>{{ $order->customer_phone_number }}</strong>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone_number) }}" 
                       class="btn btn-sm btn-success mt-1" 
                       target="_blank">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                    </a>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Alamat</small>
                    <p class="mb-0">{{ $order->customer_address }}</p>
                </div>
                @if($order->user)
                <div class="mb-0">
                    <small class="text-muted d-block">Akun Pengguna</small>
                    <strong>{{ $order->user->username }}</strong>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Proof -->
        @if($order->payment_proof)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-image me-2"></i>Bukti Pembayaran</h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                     class="img-fluid rounded mb-3" 
                     alt="Bukti Pembayaran"
                     style="max-height: 300px; cursor: pointer;"
                     onclick="window.open('{{ route('admin.orders.payment-proof', $order) }}', '_blank')">
                <div class="d-grid">
                    <a href="{{ route('admin.orders.payment-proof', $order) }}" 
                       class="btn btn-sm btn-outline-primary" 
                       target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Lihat Full Size
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Belum ada bukti pembayaran</p>
            </div>
        </div>
        @endif

        <!-- Cancellation Info -->
        @if($order->status === \App\Models\Order::STATUS_CANCEL && $order->cancelledTransaction)
        <div class="card border-0 shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="fas fa-times-circle me-2"></i>Informasi Pembatalan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal Pembatalan</small>
                    <strong>{{ $order->cancelledTransaction->cancellation_date->format('d M Y, H:i') }}</strong>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Alasan Pembatalan</small>
                    <p class="mb-0">{{ $order->cancelledTransaction->cancellation_reason }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Order Details & Actions -->
    <div class="col-md-8">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Update Status -->
                    @if($order->status !== \App\Models\Order::STATUS_CANCEL && $order->status !== \App\Models\Order::STATUS_DONE)
                    <div class="col-md-6">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="form-label small fw-bold">Update Status</label>
                            <div class="input-group">
                                <select name="status" class="form-select form-select-sm" required>
                                    @foreach(\App\Models\Order::getStatuses() as $key => $value)
                                        @if($key !== \App\Models\Order::STATUS_CANCEL)
                                            <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <!-- Update Payment -->
                    @if($order->status !== \App\Models\Order::STATUS_CANCEL)
                    <div class="col-md-6">
                        <form action="{{ route('admin.orders.update-payment', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="form-label small fw-bold">Update Pembayaran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" 
                                       name="amount_paid" 
                                       class="form-control form-control-sm" 
                                       value="{{ $order->amount_paid }}"
                                       min="0"
                                       step="1000"
                                       required>
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <!-- Confirm Payment Button -->
                    @if($order->canBeConfirmed())
                    <div class="col-md-12">
                        <form action="{{ route('admin.orders.confirm-payment', $order) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran pesanan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                    @endif

                    <!-- Cancel Order Button -->
                    @if($order->canBeCancelled())
                    <div class="col-md-12">
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times-circle me-2"></i>Batalkan Pesanan
                        </button>
                    </div>
                    @endif

                    <!-- Delete Order Button (Only for cancelled) -->
                    @if($order->status === \App\Models\Order::STATUS_CANCEL)
                    <div class="col-md-12">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="showDeleteModal()">
                            <i class="fas fa-trash me-2"></i>Hapus Pesanan
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <!-- Order Items -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Item Pesanan ({{ $order->orderItems->count() }})</h6>
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
                        <span class="badge bg-secondary">Quantity: {{ $item->quantity }}</span>
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

                <!-- Customization Details (Compact) -->
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
                        
                        @if($item->text_right)
                            <div class="col-md-12">
                                <div class="custom-badge">
                                    <i class="fas fa-align-right fa-xs text-dark me-1"></i>
                                    <strong>Teks Kanan:</strong> {{ $item->text_right }}
                                </div>
                            </div>
                        @endif

                        @if($item->text_left)
                            <div class="col-md-12">
                                <div class="custom-badge">
                                    <i class="fas fa-align-left fa-xs text-dark me-1"></i>
                                    <strong>Teks Kiri:</strong> {{ $item->text_left }}
                                </div>
                            </div>
                        @endif

                        @if($item->text_single)
                            <div class="col-md-12">
                                <div class="custom-badge">
                                    <i class="fas fa-align-center fa-xs text-dark me-1"></i>
                                    <strong>Teks Tunggal:</strong> {{ $item->text_single }}
                                </div>
                            </div>
                        @endif

                        @if($item->logo_path)
                            <div class="col-md-12">
                                <div class="custom-badge d-flex align-items-center">
                                    <i class="fas fa-image fa-xs text-dark me-1"></i>
                                    <strong>Logo:</strong> 
                                    <img src="{{ asset('storage/' . $item->logo_path) }}" alt="Logo" style="max-height: 50px; vertical-align: middle; margin-left: 8px;">
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
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
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2 text-danger"></i>Batalkan Pesanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Apakah Anda yakin ingin membatalkan pesanan <strong>{{ $order->order_number }}</strong>?
                    </div>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">
                            Alasan Pembatalan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="cancellation_reason" 
                                  name="cancellation_reason" 
                                  rows="4"
                                  placeholder="Masukkan alasan pembatalan pesanan..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-2"></i>Ya, Batalkan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Order Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <div class="delete-icon-wrapper">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                    </div>
                </div>
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Pesanan</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus pesanan:</p>
                <p class="fw-bold mb-3">{{ $order->order_number }}</p>
                <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <small>Data pesanan akan dihapus permanen!</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </button>
                </form>
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

.order-item-card:hover {
    background-color: #f8f9fa;
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

.delete-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: rgba(220, 53, 69, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
function showDeleteModal() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush