@extends('layouts.owner')

@section('title', 'Detail Invoice Pembelian')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-file-invoice me-2 text-success"></i>Detail Invoice Pembelian</h2>
        <p class="text-muted mb-0">{{ $purchaseInvoice->invoice_number }}</p>
    </div>
    <div>
        <a href="{{ route('owner.purchase-invoices.edit', $purchaseInvoice) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <button type="button" 
                class="btn btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal">
            <i class="fas fa-trash me-2"></i>Hapus
        </button>
        <a href="{{ route('owner.purchase-invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Invoice Details -->
    <div class="col-lg-8">
        <!-- Invoice Header Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Invoice</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nomor Invoice</small>
                        <strong class="text-primary">{{ $purchaseInvoice->invoice_number }}</strong>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Tanggal Invoice</small>
                        <strong>
                            <i class="fas fa-calendar me-2 text-muted"></i>
                            {{ $purchaseInvoice->invoice_date->format('d F Y') }}
                        </strong>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dibuat Pada</small>
                        <strong>{{ $purchaseInvoice->created_at->format('d M Y H:i') }}</strong>
                        <br><small class="text-muted">{{ $purchaseInvoice->created_at->diffForHumans() }}</small>
                    </div>
                    
                    <div class="col-md-6 mb-0">
                        <small class="text-muted d-block">Terakhir Update</small>
                        <strong>{{ $purchaseInvoice->updated_at->format('d M Y H:i') }}</strong>
                        <br><small class="text-muted">{{ $purchaseInvoice->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detail Item ({{ $purchaseInvoice->total_items }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Tipe Item</th>
                                <th>Nama Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseInvoice->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $detail->item_type_text }}</span>
                                </td>
                                <td>
                                    <strong>{{ $detail->item_name }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ number_format($detail->quantity) }}</span>
                                </td>
                                <td class="text-end">
                                    {{ $detail->formatted_unit_price }}
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">{{ $detail->formatted_subtotal }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">TOTAL:</th>
                                <th class="text-center">
                                    <span class="badge bg-primary">{{ number_format($purchaseInvoice->total_quantity) }}</span>
                                </th>
                                <th></th>
                                <th class="text-end">
                                    <h5 class="mb-0 text-success">{{ $purchaseInvoice->formatted_total_amount }}</h5>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Sidebar -->
    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <small class="text-muted d-block">Total Item</small>
                        <h4 class="mb-0">{{ number_format($purchaseInvoice->total_items) }}</h4>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-boxes fa-2x text-secondary"></i>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <small class="text-muted d-block">Total Quantity</small>
                        <h4 class="mb-0">{{ number_format($purchaseInvoice->total_quantity) }}</h4>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-cubes fa-2x text-primary"></i>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block">Total Nilai</small>
                        <h3 class="mb-0 text-success">{{ $purchaseInvoice->formatted_total_amount }}</h3>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Type Breakdown -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Breakdown Tipe Item</h6>
            </div>
            <div class="card-body">
                @php
                    $typeBreakdown = $purchaseInvoice->details->groupBy('item_type_text');
                @endphp
                
                @foreach($typeBreakdown as $type => $items)
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <small class="text-muted d-block">{{ $type }}</small>
                        <strong>{{ $items->count() }} item</strong>
                    </div>
                    <div class="text-end">
                        <strong class="text-success">
                            Rp {{ number_format($items->sum('subtotal'), 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Hapus Invoice
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                </div>
                <p class="text-center">
                    Apakah Anda yakin ingin <strong class="text-danger">MENGHAPUS PERMANEN</strong> invoice ini? 
                </p>
                <div class="alert alert-light border">
                    <strong>{{ $purchaseInvoice->invoice_number }}</strong><br>
                    <small class="text-muted">
                        Tanggal: {{ $purchaseInvoice->invoice_date->format('d M Y') }}<br>
                        Total: {{ $purchaseInvoice->formatted_total_amount }}<br>
                        Items: {{ $purchaseInvoice->total_items }}
                    </small>
                </div>
                <div class="alert alert-danger mb-0">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>PERINGATAN:</strong> Aksi ini tidak dapat dibatalkan!  Semua data item akan ikut terhapus.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('owner.purchase-invoices.destroy', $purchaseInvoice) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection