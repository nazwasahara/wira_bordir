@extends('layouts.admin')

@section('title', 'Manajemen Opsi Pita Motif')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-wind me-2"></i>Manajemen Opsi Pita Motif</h2>
    <a href="{{ route('services.motif-ribbon-options.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Opsi Baru
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Opsi</h6>
                <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Ukuran Kecil</h6>
                <h3 class="mb-0 fw-bold text-info">{{ $stats['small'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Ukuran Sedang</h6>
                <h3 class="mb-0 fw-bold text-primary">{{ $stats['medium'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Ukuran Besar</h6>
                <h3 class="mb-0 fw-bold text-success">{{ $stats['large'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('services.motif-ribbon-options.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari berdasarkan warna..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Ukuran</label>
                <select name="size" class="form-select">
                    <option value="">Semua Ukuran</option>
                    <option value="small" {{ request('size') == 'small' ? 'selected' : '' }}>Kecil</option>
                    <option value="medium" {{ request('size') == 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="large" {{ request('size') == 'large' ? 'selected' : '' }}>Besar</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="color" {{ request('sort_by') == 'color' ? 'selected' : '' }}>Warna</option>
                    <option value="size" {{ request('sort_by') == 'size' ? 'selected' : '' }}>Ukuran</option>
                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Harga</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutan</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('services.motif-ribbon-options.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Motif Ribbon Options Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Opsi Pita Motif</h5>
        <div>
            <span class="text-muted">Menampilkan {{ $motifRibbonOptions->firstItem() ?? 0 }} sampai {{ $motifRibbonOptions->lastItem() ?? 0 }} dari {{ $motifRibbonOptions->total() }} data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Warna</th>
                        <th>Ukuran</th>
                        <th>Harga</th>
                        <th>Dibuat</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($motifRibbonOptions as $motifRibbonOption)
                    <tr>
                        <td>{{ $motifRibbonOptions->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="motif-ribbon-icon bg-primary text-white me-2">
                                    <i class="fas fa-wind"></i>
                                </div>
                                <strong>{{ $motifRibbonOption->color }}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $motifRibbonOption->size_badge_color }}">
                                {{ $motifRibbonOption->size_indonesia }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-success">{{ $motifRibbonOption->formatted_price }}</strong>
                        </td>
                        <td>{{ $motifRibbonOption->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('services.motif-ribbon-options.show', $motifRibbonOption) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('services.motif-ribbon-options.edit', $motifRibbonOption) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="showDeleteModal({{ $motifRibbonOption->id }}, '{{ $motifRibbonOption->color }} - {{ $motifRibbonOption->size_indonesia }}')"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $motifRibbonOption->id }}" 
                                      action="{{ route('services.motif-ribbon-options.destroy', $motifRibbonOption) }}" 
                                      method="POST" 
                                      class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-wind fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada opsi pita motif ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($motifRibbonOptions->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $motifRibbonOptions->firstItem() }} sampai {{ $motifRibbonOptions->lastItem() }} dari {{ $motifRibbonOptions->total() }} data
            </div>
            <div>
                {{ $motifRibbonOptions->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus opsi pita motif:</p>
                <p class="fw-bold mb-3" id="deleteMotifRibbonOptionName"></p>
                <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Tindakan ini tidak dapat dibatalkan!</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.motif-ribbon-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
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
let deleteMotifRibbonOptionId = null;

function showDeleteModal(motifRibbonOptionId, motifRibbonOptionName) {
    deleteMotifRibbonOptionId = motifRibbonOptionId;
    document.getElementById('deleteMotifRibbonOptionName').textContent = motifRibbonOptionName;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    if (deleteMotifRibbonOptionId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        document.getElementById('delete-form-' + deleteMotifRibbonOptionId).submit();
    }
});

document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    deleteMotifRibbonOptionId = null;
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush