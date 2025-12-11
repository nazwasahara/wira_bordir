@extends('layouts.admin')

@section('title', 'Manajemen Motif Samping')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-shapes me-2"></i>Manajemen Motif Samping</h2>
    <a href="{{ route('services.side-motifs.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Motif Baru
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Motif</h6>
                <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Digunakan</h6>
                <h3 class="mb-0 fw-bold text-success">{{ $stats['used_in_orders'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Nilai</h6>
                <h3 class="mb-0 fw-bold text-info">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Rata-rata Harga</h6>
                <h3 class="mb-0 fw-bold text-primary">Rp {{ number_format($stats['average_price'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('services.side-motifs.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari berdasarkan nama motif..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama Motif</option>
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
                <a href="{{ route('services.side-motifs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Side Motifs Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Motif Samping</h5>
        <div>
            <span class="text-muted">Menampilkan {{ $sideMotifs->firstItem() ?? 0 }} sampai {{ $sideMotifs->lastItem() ?? 0 }} dari {{ $sideMotifs->total() }} data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama Motif</th>
                        <th>Harga</th>
                        <th>Dibuat</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sideMotifs as $sideMotif)
                    <tr>
                        <td>{{ $sideMotifs->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="motif-icon bg-primary text-white me-2">
                                    <i class="fas fa-shapes"></i>
                                </div>
                                <strong>{{ $sideMotif->name }}</strong>
                            </div>
                        </td>
                        <td>
                            <strong class="text-success">{{ $sideMotif->formatted_price }}</strong>
                        </td>
                        <td>{{ $sideMotif->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('services.side-motifs.show', $sideMotif) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('services.side-motifs.edit', $sideMotif) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="showDeleteModal({{ $sideMotif->id }}, '{{ $sideMotif->name }}')"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $sideMotif->id }}" 
                                      action="{{ route('services.side-motifs.destroy', $sideMotif) }}" 
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
                        <td colspan="5" class="text-center py-4">
                            <i class="fas fa-shapes fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada motif samping ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sideMotifs->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $sideMotifs->firstItem() }} sampai {{ $sideMotifs->lastItem() }} dari {{ $sideMotifs->total() }} data
            </div>
            <div>
                {{ $sideMotifs->links() }}
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
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus motif samping:</p>
                <p class="fw-bold mb-3" id="deleteSideMotifName"></p>
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
.motif-icon {
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

.btn {
    transition: all 0.3s ease;
}
</style>
@endpush

@push('scripts')
<script>
let deleteSideMotifId = null;

function showDeleteModal(sideMotifId, sideMotifName) {
    deleteSideMotifId = sideMotifId;
    document.getElementById('deleteSideMotifName').textContent = sideMotifName;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    if (deleteSideMotifId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        document.getElementById('delete-form-' + deleteSideMotifId).submit();
    }
});

document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    deleteSideMotifId = null;
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush