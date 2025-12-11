@extends('layouts.admin')

@section('title', 'Manajemen Gallery')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-images me-2"></i>Manajemen Gallery</h2>
    <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tambah Gallery Baru
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Gallery</h6>
                <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.galleries.index') }}" method="GET" class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari berdasarkan judul atau deskripsi..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutkan</label>
                <select name="sort_by" class="form-select">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Judul</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Urutan</label>
                <select name="sort_order" class="form-select">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓ Terbaru</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑ Terlama</option>
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Galleries Grid -->
<div class="row g-3">
    @forelse($galleries as $gallery)
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100 gallery-card">
            <div class="gallery-image-wrapper">
                @if($gallery->hasImage())
                    <img src="{{ $gallery->image_url }}" 
                         class="card-img-top gallery-image" 
                         alt="{{ $gallery->title }}"
                         onclick="showImageModal('{{ $gallery->image_url }}', '{{ $gallery->title }}')">
                @else
                    <div class="no-image">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <h5 class="card-title mb-2">{{ $gallery->title }}</h5>
                <p class="card-text text-muted small mb-3" style="min-height: 40px;">
                    {{ Str::limit($gallery->description, 80) ?: 'Tidak ada deskripsi' }}
                </p>
                <div class="text-muted small mb-3">
                    <i class="fas fa-calendar me-1"></i>{{ $gallery->created_at->format('d M Y') }}
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.galleries.show', $gallery) }}" 
                       class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-eye me-1"></i>Detail
                    </a>
                    <a href="{{ route('admin.galleries.edit', $gallery) }}" 
                       class="btn btn-sm btn-outline-secondary flex-fill">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="showDeleteModal({{ $gallery->id }}, '{{ $gallery->title }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <form id="delete-form-{{ $gallery->id }}" 
                  action="{{ route('admin.galleries.destroy', $gallery) }}" 
                  method="POST" 
                  class="d-none">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada gallery ditemukan</h5>
                <p class="text-muted mb-3">Mulai dengan menambahkan gallery pertama Anda</p>
                <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Gallery Baru
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($galleries->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $galleries->links() }}
</div>
@endif

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="imageModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <img id="modalImage" src="" alt="" class="img-fluid w-100">
            </div>
        </div>
    </div>
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
                <h4 class="mb-3 fw-bold">Konfirmasi Hapus Gallery</h4>
                <p class="text-muted mb-1">Apakah Anda yakin ingin menghapus gallery:</p>
                <p class="fw-bold mb-3" id="deleteGalleryTitle"></p>
                <div class="alert alert-danger d-flex align-items-start mb-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div class="text-start">
                        <small class="d-block mb-1"><strong>Perhatian!</strong></small>
                        <small>
                            • Gambar akan dihapus permanen<br>
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
                    <i class="fas fa-trash me-2"></i>Ya, Hapus Gallery
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.gallery-card {
    transition: all 0.3s ease;
}

.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
}

.gallery-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 75%; /* 4:3 Aspect Ratio */
    overflow: hidden;
    background: #f8f9fa;
}

.gallery-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.gallery-image:hover {
    transform: scale(1.05);
}

.no-image {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
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
let deleteGalleryId = null;

function showImageModal(imageUrl, title) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModalLabel').textContent = title;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

function showDeleteModal(galleryId, galleryTitle) {
    deleteGalleryId = galleryId;
    document.getElementById('deleteGalleryTitle').textContent = galleryTitle;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    if (deleteGalleryId) {
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
        this.disabled = true;
        document.getElementById('delete-form-' + deleteGalleryId).submit();
    }
});

document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    deleteGalleryId = null;
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus Gallery';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush