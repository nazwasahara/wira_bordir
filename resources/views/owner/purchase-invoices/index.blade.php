@extends('layouts.owner')

@section('title', 'Invoice Pembelian')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Invoice Pembelian</h2>
        <p class="text-muted mb-0">Kelola invoice pembelian barang</p>
    </div>
    <div>
        <a href="{{ route('owner.purchase-invoices.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Buat Invoice
        </a>
        <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <i class="fas fa-file-invoice fa-2x text-primary mb-2"></i>
                <h6 class="text-muted mb-1">Total Invoice</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_invoices']) }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <i class="fas fa-calendar-day fa-2x text-success mb-2"></i>
                <h6 class="text-muted mb-1">Bulan Ini</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['this_month']) }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <i class="fas fa-dollar-sign fa-2x text-info mb-2"></i>
                <h6 class="text-muted mb-1">Total Nilai</h6>
                <h3 class="mb-0 fw-bold">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-3">
                <i class="fas fa-box fa-2x text-secondary mb-2"></i>
                <h6 class="text-muted mb-1">Total Item</h6>
                <h3 class="mb-0 fw-bold">{{ number_format($stats['total_items']) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter & Pencarian</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.purchase-invoices.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari Invoice</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="ID atau tanggal..." 
                       value="{{ request('search') }}">
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
            
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Buat</option>
                    <option value="invoice_date" {{ request('sort_by') == 'invoice_date' ?  'selected' : '' }}>Tanggal Invoice</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <label class="form-label">Urutan</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑</option>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('owner.purchase-invoices.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Invoice ({{ $invoices->total() }})</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th width="50">#</th>
                        <th>No.  Invoice</th>
                        <th>Tanggal Invoice</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-end">Total Nilai</th>
                        <th class="text-center">Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $index => $invoice)
                    <tr>
                        <td>{{ $invoices->firstItem() + $index }}</td>
                        <td>
                            <strong class="text-primary">{{ $invoice->invoice_number }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-calendar me-2 text-muted"></i>
                            {{ $invoice->invoice_date->format('d M Y') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $invoice->total_items }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ number_format($invoice->total_quantity) }}</span>
                        </td>
                        <td class="text-end">
                            <strong class="text-success">{{ $invoice->formatted_total_amount }}</strong>
                        </td>
                        <td class="text-center">
                            <small>{{ $invoice->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('owner.purchase-invoices.show', $invoice) }}" 
                                   class="btn btn-outline-primary"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('owner.purchase-invoices.edit', $invoice) }}" 
                                   class="btn btn-outline-warning"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $invoice->id }}"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada invoice pembelian</p>
                            <a href="{{ route('owner.purchase-invoices.create') }}" class="btn btn-success btn-sm mt-3">
                                <i class="fas fa-plus me-2"></i>Buat Invoice Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($invoices->hasPages())
        <div class="p-3 border-top">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Modals -->
@foreach($invoices as $invoice)
<div class="modal fade" id="deleteModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
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
                    Apakah Anda yakin ingin <strong class="text-danger">MENGHAPUS</strong> invoice ini?
                </p>
                <div class="alert alert-light border">
                    <strong>{{ $invoice->invoice_number }}</strong><br>
                    <small class="text-muted">
                        Tanggal: {{ $invoice->invoice_date->format('d M Y') }}<br>
                        Total: {{ $invoice->formatted_total_amount }}<br>
                        Items: {{ $invoice->total_items }}
                    </small>
                </div>
                <div class="alert alert-danger mb-0">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Aksi ini tidak dapat dibatalkan! 
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('owner.purchase-invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
. sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa;
}
</style>
@endpush