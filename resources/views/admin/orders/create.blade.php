@extends('layouts.admin')

@section('title', 'Buat Pesanan Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Buat Pesanan Baru</h2>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Customer (Opsional)</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" 
                                id="user_id" 
                                name="user_id">
                            <option value="">- Customer Baru -</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" 
                                        data-name="{{ $user->username }}"
                                        data-phone="{{ $user->phone_number }}"
                                        data-address="{{ $user->address }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih dari customer yang sudah terdaftar atau isi manual</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label for="customer_name" class="form-label">
                            Nama Pelanggan <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" 
                               name="customer_name" 
                               value="{{ old('customer_name') }}"
                               required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone_number" class="form-label">
                            Nomor Telepon <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('customer_phone_number') is-invalid @enderror" 
                               id="customer_phone_number" 
                               name="customer_phone_number" 
                               value="{{ old('customer_phone_number') }}"
                               required>
                        @error('customer_phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_address" class="form-label">
                            Alamat <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                  id="customer_address" 
                                  name="customer_address" 
                                  rows="3"
                                  required>{{ old('customer_address') }}</textarea>
                        @error('customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Status Pesanan <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            @foreach(\App\Models\Order::getStatuses() as $key => $value)
                                <option value="{{ $key }}" {{ old('status', 'pending') == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                      <label for="payment_proof" class="form-label">
                          <i class="fas fa-image me-1"></i>Bukti Pembayaran (Opsional)
                      </label>
                      <input type="file" 
                            class="form-control @error('payment_proof') is-invalid @enderror" 
                            id="payment_proof" 
                            name="payment_proof"
                            accept="image/jpeg,image/jpg,image/png">
                      @error('payment_proof')
                          <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                      <small class="text-muted d-block mt-1">
                          <i class="fas fa-info-circle me-1"></i>Format: JPG, JPEG, PNG (Max: 2MB)
                      </small>
                      
                      <!-- Image Preview -->
                      <div id="imagePreview" class="mt-2" style="display: none;">
                          <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                          <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                              <i class="fas fa-times me-1"></i>Hapus
                          </button>
                      </div>
                  </div>

                    <hr>

                    <!-- Total Preview -->
                    <div class="alert alert-info mb-0">
                        <h6 class="mb-2">Total Pesanan:</h6>
                        <h4 class="mb-0 text-primary" id="totalPrice">Rp 0</h4>
                        <small class="text-muted" id="totalItems">0 item</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Item Pesanan</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                        <i class="fas fa-plus me-1"></i>Tambah Item
                    </button>
                </div>
                <div class="card-body" id="itemsContainer">
                    <!-- Items will be added here dynamically -->
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Pesanan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Item Template (Hidden) -->
<template id="itemTemplate">
    <div class="item-card border rounded p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Item <span class="item-number">1</span></h6>
            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Produk <span class="text-danger">*</span></label>
                <select class="form-select product-select" name="items[0][product_id]" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->base_price }}">
                            {{ $product->product_name }} - {{ $product->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" class="form-control quantity-input" name="items[0][quantity]" value="1" min="1" required>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Material</label>
                <select class="form-select customization-select" name="items[0][material_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" data-price="{{ $material->price }}">
                            {{ $material->name }} - {{ $material->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Warna Material</label>
                <select class="form-select customization-select" name="items[0][material_color_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($materialColors as $color)
                        <option value="{{ $color->id }}" data-price="{{ $color->price }}">
                            {{ $color->name }} - {{ $color->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Tipe Sash</label>
                <select class="form-select customization-select" name="items[0][sash_type_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($sashTypes as $type)
                        <option value="{{ $type->id }}" data-price="{{ $type->price }}">
                            {{ $type->name }} - {{ $type->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Font</label>
                <select class="form-select customization-select" name="items[0][font_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($fonts as $font)
                        <option value="{{ $font->id }}" data-price="{{ $font->price }}">
                            {{ $font->name }} - {{ $font->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Motif Samping</label>
                <select class="form-select customization-select" name="items[0][side_motif_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($sideMotifs as $motif)
                        <option value="{{ $motif->id }}" data-price="{{ $motif->price }}">
                            {{ $motif->name }} - {{ $motif->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Warna Pita</label>
                <select class="form-select customization-select" name="items[0][ribbon_color_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($ribbonColors as $color)
                        <option value="{{ $color->id }}" data-price="{{ $color->price }}">
                            {{ $color->name }} - {{ $color->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Renda</label>
                <select class="form-select customization-select" name="items[0][lace_option_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($laceOptions as $lace)
                        <option value="{{ $lace->id }}" data-price="{{ $lace->price }}">
                            {{ $lace->color }} ({{ $lace->size_indonesia }}) - {{ $lace->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Rombe</label>
                <select class="form-select customization-select" name="items[0][rombe_option_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($rombeOptions as $rombe)
                        <option value="{{ $rombe->id }}" data-price="{{ $rombe->price }}">
                            {{ $rombe->color }} ({{ $rombe->size_indonesia }}) - {{ $rombe->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Pita Motif</label>
                <select class="form-select customization-select" name="items[0][motif_ribbon_option_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($motifRibbonOptions as $motifRibbon)
                        <option value="{{ $motifRibbon->id }}" data-price="{{ $motifRibbon->price }}">
                            {{ $motifRibbon->color }} ({{ $motifRibbon->size_indonesia }}) - {{ $motifRibbon->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Item Tambahan</label>
                <select class="form-select customization-select" name="items[0][additional_item_option_id]">
                    <option value="">- Tidak Ada -</option>
                    @foreach($additionalItemOptions as $option)
                        <option value="{{ $option->id }}" data-price="{{ $option->price }}">
                            {{ $option->additionalItem->name ?? 'N/A' }} - {{ $option->color }} ({{ $option->model }}) - {{ $option->formatted_price }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Teks Kanan (Opsional)</label>
                <input type="text" class="form-control" name="items[0][text_right]" value="{{ old('items.0.text_right') }}" maxlength="255">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Teks Kiri (Opsional)</label>
                <input type="text" class="form-control" name="items[0][text_left]" value="{{ old('items.0.text_left') }}" maxlength="255">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Teks Tunggal (Opsional)</label>
                <input type="text" class="form-control" name="items[0][text_single]" value="{{ old('items.0.text_single') }}" maxlength="255">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label"><i class="fas fa-image me-1"></i>Logo (Opsional)</label>
                <input type="file" class="form-control item-logo-path" name="items[0][logo_path]" accept="image/jpeg,image/jpg,image/png">
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle me-1"></i>Format: JPG, JPEG, PNG (Max: 2MB)
                </small>
                <div class="logo-preview mt-2" style="display: none;">
                    <img src="" alt="Logo Preview" class="img-thumbnail" style="max-height: 100px;">
                    <button type="button" class="btn btn-sm btn-danger mt-2 remove-item-logo-btn">
                        <i class="fas fa-times me-1"></i>Hapus Logo
                    </button>
                </div>
            </div>
        </div>

        <div class="alert alert-info mb-0">
            <div class="d-flex justify-content-between">
                <span>Harga per Item:</span>
                <strong class="item-price">Rp 0</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <strong class="item-subtotal text-primary">Rp 0</strong>
            </div>
        </div>
    </div>
</template>
@endsection

@push('styles')
<style>
.item-card {
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.item-card:hover {
    background: #e9ecef;
}

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
let itemIndex = 0;

// Add Item Button
document.getElementById('addItemBtn').addEventListener('click', function() {
    addItem();
});

// Add first item on page load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
    
    // Auto fill customer data when user is selected
    document.getElementById('user_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('customer_name').value = selectedOption.dataset.name || '';
            document.getElementById('customer_phone_number').value = selectedOption.dataset.phone || '';
            document.getElementById('customer_address').value = selectedOption.dataset.address || '';
        }
    });
});

function setupItemLogo(newItem) {
    const logoInput = newItem.querySelector('.item-logo-path');
    const logoPreviewContainer = newItem.querySelector('.logo-preview');
    const logoPreviewImg = newItem.querySelector('.logo-preview img');
    const removeLogoBtn = newItem.querySelector('.remove-item-logo-btn');

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2048000) { // 2MB
                alert('Ukuran file logo maksimal 2MB!');
                this.value = '';
                logoPreviewContainer.style.display = 'none';
                return;
            }
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Format file logo harus JPG, JPEG, atau PNG!');
                this.value = '';
                logoPreviewContainer.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreviewImg.src = e.target.result;
                logoPreviewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            logoPreviewContainer.style.display = 'none';
            logoPreviewImg.src = '';
        }
    });

    removeLogoBtn.addEventListener('click', function() {
        logoInput.value = '';
        logoPreviewContainer.style.display = 'none';
        logoPreviewImg.src = '';
    });
}

function addItem() {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update item number
    clone.querySelector('.item-number').textContent = itemIndex + 1;
    
    // Update all name attributes with correct index
    clone.querySelectorAll('[name^="items[0]"]').forEach(function(input) {
        input.name = input.name.replace('items[0]', `items[${itemIndex}]`);
        // Clear old values from template
        if (input.tagName === 'INPUT' && input.type === 'text') {
            input.value = '';
        }
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0; // Reset select to first option
        }
    });

    // Handle logo input name attribute separately as it uses "items[0][logo_path]"
    const logoInput = clone.querySelector('.item-logo-path');
    if (logoInput) {
        logoInput.name = `items[${itemIndex}][logo_path]`;
    }
    
    // Add to container
    document.getElementById('itemsContainer').appendChild(clone);
    
    // Get the newly added item
    const items = document.querySelectorAll('.item-card');
    const newItem = items[items.length - 1];
    
    // Add event listeners for price calculation
    newItem.querySelectorAll('.product-select, .customization-select, .quantity-input').forEach(function(select) {
        select.addEventListener('change', function() {
            calculateItemPrice(newItem);
            calculateTotalPrice();
        });
        select.addEventListener('input', function() {
            calculateItemPrice(newItem);
            calculateTotalPrice();
        });
    });
    
    // Add remove button functionality
    newItem.querySelector('.remove-item-btn').addEventListener('click', function() {
        if (document.querySelectorAll('.item-card').length > 1) {
            newItem.remove();
            updateItemNumbers();
            calculateTotalPrice();
        } else {
            alert('Minimal harus ada 1 item!');
        }
    });
    
    // Setup logo handling for the new item
    setupItemLogo(newItem);

    itemIndex++;
    calculateTotalPrice();
}

function calculateItemPrice(itemCard) {
    let price = 0;
    
    // Get all selects with data-price
    itemCard.querySelectorAll('select[data-price], select option:checked').forEach(function(element) {
        if (element.value && element.dataset.price) {
            price += parseFloat(element.dataset.price);
        }
    });
    
    const quantity = parseInt(itemCard.querySelector('.quantity-input').value) || 1;
    const subtotal = price * quantity;
    
    // Update display
    itemCard.querySelector('.item-price').textContent = formatPrice(price);
    itemCard.querySelector('.item-subtotal').textContent = formatPrice(subtotal);
}

function calculateTotalPrice() {
    let total = 0;
    let itemCount = 0;
    
    document.querySelectorAll('.item-card').forEach(function(item) {
        const subtotalText = item.querySelector('.item-subtotal').textContent;
        const subtotal = parseFloat(subtotalText.replace(/[^0-9]/g, ''));
        total += subtotal;
        
        const quantity = parseInt(item.querySelector('.quantity-input').value) || 1;
        itemCount += quantity;
    });
    
    document.getElementById('totalPrice').textContent = formatPrice(total);
    document.getElementById('totalItems').textContent = itemCount + ' item';
}

function updateItemNumbers() {
    document.querySelectorAll('.item-card').forEach(function(item, index) {
        item.querySelector('.item-number').textContent = index + 1;
    });
}

function formatPrice(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Form submit
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

document.getElementById('payment_proof')?.addEventListener('change', function(e) {
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
    document.getElementById('payment_proof').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
}
</script>
@endpush