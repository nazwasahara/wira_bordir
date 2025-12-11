@extends('layouts.admin')

@section('title', 'Edit Gallery')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Gallery</h2>
    <div>
        <a href="{{ route('admin.galleries.show', $gallery) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data" id="galleryForm">
            @csrf
            @method('PUT')
            
            <!-- Gallery Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Gallery</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Judul <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $gallery->title) }}" 
                               placeholder="Masukkan judul gallery"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Deskripsi
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Masukkan deskripsi gallery (opsional)">{{ old('description', $gallery->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Jelaskan tentang gambar ini</small>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">
                            <i class="fas fa-image me-1"></i>Gambar
                        </label>
                        
                        @if($gallery->hasImage())
                            <!-- Existing Image -->
                            <div class="card border-0 bg-light mb-2">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $gallery->image_url }}" 
                                                 alt="{{ $gallery->title }}" 
                                                 class="img-thumbnail me-2" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                            <div>
                                                <small class="d-block text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Gambar sudah diupload
                                                </small>
                                                <a href="{{ $gallery->image_url }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-link p-0">
                                                    <i class="fas fa-external-link-alt me-1"></i>Lihat Full Size
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="remove_image" 
                                                   name="remove_image" 
                                                   value="1">
                                            <label class="form-check-label small text-danger" for="remove_image">
                                                Hapus
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Upload New -->
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image"
                               accept="image/jpeg,image/jpg,image/png">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Format: JPG, JPEG, PNG (Max: 2MB)
                            @if($gallery->hasImage())
                                <br><i class="fas fa-arrow-up me-1"></i>Upload file baru untuk mengganti gambar yang ada
                            @endif
                        </small>
                        
                        <!-- New Image Preview -->
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <label class="form-label">Preview Gambar Baru:</label>
                            <div class="position-relative d-inline-block">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 300px;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="removeImage()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <small class="text-muted d-block">Dibuat Pada</small>
                            <strong>{{ $gallery->created_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $gallery->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Terakhir Diperbarui</small>
                            <strong>{{ $gallery->updated_at->format('d M Y H:i') }}</strong>
                            <small class="d-block text-muted">{{ $gallery->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button>
                        <a href="{{ route('admin.galleries.show', $gallery) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Sidebar -->
    <div class="col-lg-4">
        <!-- Current Image -->
        @if($gallery->hasImage())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-image me-2"></i>Gambar Saat Ini</h6>
            </div>
            <div class="card-body p-0">
                <img src="{{ $gallery->image_url }}" 
                     alt="{{ $gallery->title }}" 
                     class="img-fluid w-100">
            </div>
        </div>
        @endif

        <!-- Tips Card -->
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-3">Panduan Edit Gallery:</h6>
                <ul class="small mb-3">
                    <li class="mb-2">Judul yang jelas membantu pencarian</li>
                    <li class="mb-2">Deskripsi meningkatkan SEO</li>
                    <li class="mb-2">Upload gambar baru untuk mengganti</li>
                    <li class="mb-2">Centang "Hapus" untuk menghapus gambar</li>
                </ul>

                <div class="alert alert-info small mb-0" role="alert">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Info:</strong> Perubahan akan langsung terlihat di halaman gallery.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.sticky-top {
    position: sticky;
    top: 20px;
    z-index: 100;
}

@media (max-width: 991px) {
    .sticky-top {
        position: relative;
        top: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Image preview
document.getElementById('image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size
        if (file.size > 2048000) { // 2MB
            alert('Ukuran file maksimal 2MB!');
            this.value = '';
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            alert('Format file harus JPG, JPEG, atau PNG!');
            this.value = '';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}

// Form validation and submit handling
document.getElementById('galleryForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Reset button confirmation
document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
    if (!confirm('Apakah Anda yakin ingin mereset form ini? Semua perubahan yang belum disimpan akan hilang.')) {
        e.preventDefault();
    } else {
        removeImage();
    }
});

// Form change detection
let formChanged = false;

document.getElementById('galleryForm')?.addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

document.getElementById('galleryForm')?.addEventListener('submit', function() {
    formChanged = false;
});

// Character counter for description
const descriptionTextarea = document.getElementById('description');
if (descriptionTextarea) {
    const counter = document.createElement('small');
    counter.className = 'text-muted d-block mt-1';
    descriptionTextarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = descriptionTextarea.value.length;
        counter.textContent = `${length} karakter`;
    }
    
    descriptionTextarea.addEventListener('input', updateCounter);
    updateCounter();
}
</script>
@endpush