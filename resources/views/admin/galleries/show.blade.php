@extends('layouts.admin')

@section('title', 'Detail Gallery')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-image me-2"></i>Detail Gallery</h2>
    <div>
        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit Gallery
        </a>
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Image Display -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                @if($gallery->hasImage())
                    <img src="{{ $gallery->image_url }}" 
                         alt="{{ $gallery->title }}" 
                         class="img-fluid w-100 rounded"
                         style="max-height: 600px; object-fit: contain; background: #f8f9fa;">
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-image fa-5x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada gambar</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-align-left me-2"></i>Deskripsi</h6>
            </div>
            <div class="card-body">
                @if($gallery->description)
                    <p class="mb-0" style="white-space: pre-line;">{{ $gallery->description }}</p>
                @else
                    <p class="text-muted mb-0">Tidak ada deskripsi</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Gallery Info -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Gallery</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">ID Gallery</small>
                    <strong>#{{ $gallery->id }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Judul</small>
                    <strong>{{ $gallery->title }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Dibuat Pada</small>
                    <strong>{{ $gallery->created_at->format('d M Y H:i') }}</strong>
                    <small class="d-block text-muted">{{ $gallery->created_at->diffForHumans() }}</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Terakhir Diperbarui</small>
                    <strong>{{ $gallery->updated_at->format('d M Y H:i') }}</strong>
                    <small class="d-block text-muted">{{ $gallery->updated_at->diffForHumans() }}</small>
                </div>

                @if($gallery->hasImage())
                <div class="mb-0">
                    <small class="text-muted d-block mb-2">File Gambar</small>
                    <a href="{{ $gallery->image_url }}" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-primary w-100 mb-2">
                        <i class="fas fa-external-link-alt me-2"></i>Buka Gambar
                    </a>
                    <a href="{{ $gallery->image_url }}" 
                       download 
                       class="btn btn-sm btn-outline-success w-100">
                        <i class="fas fa-download me-2"></i>Download Gambar
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Aksi</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Gallery
                    </a>
                    
                    <button type="button" 
                            class="btn btn-danger" 
                            onclick="showDeleteModal('{{ $gallery->title }}')">
                        <i class="fas fa-trash me-2"></i>Hapus Gallery
                    </button>
                    
                    <form id="delete-form" 
                          action="{{ route('admin.galleries.destroy', $gallery) }}" 
                          method="POST" 
                          class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
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
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
</style>
@endpush

@push('scripts')
<script>
function showDeleteModal(galleryTitle) {
    document.getElementById('deleteGalleryTitle').textContent = galleryTitle;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
    this.disabled = true;
    document.getElementById('delete-form').submit();
});

document.getElementById('deleteModal')?.addEventListener('hidden.bs.modal', function() {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Ya, Hapus';
        confirmBtn.disabled = false;
    }
});
</script>
@endpush