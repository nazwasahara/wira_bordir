@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-edit me-2"></i>Edit Pesanan
        <span class="badge bg-{{ $order->status_badge_color }}">{{ $order->status_text }}</span>
    </h2>
    <div>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<form action="{{ route('admin.orders.update', $order) }}" method="POST" id="orderForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Customer Information -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Pesanan</label>
                        <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                    </div>

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
                                        {{ old('user_id', $order->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                               value="{{ old('customer_name', $order->customer_name) }}"
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
                               value="{{ old('customer_phone_number', $order->customer_phone_number) }}"
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
                                  required>{{ old('customer_address', $order->customer_address) }}</textarea>
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
                                <option value="{{ $key }}" {{ old('status', $order->status) == $key ? 'selected' : '' }}>
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
                            <i class="fas fa-image me-1"></i>Bukti Pembayaran
                        </label>
                        
                        @if($order->payment_proof)
                            <!-- Existing Payment Proof -->
                            <div class="card border-0 bg-light mb-2">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                                                 alt="Bukti Pembayaran" 
                                                 class="img-thumbnail me-2" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <small class="d-block text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Bukti pembayaran sudah diupload
                                                </small>
                                                <a href="{{ asset('storage/' . $order->payment_proof) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-link p-0">
                                                    <i class="fas fa-external-link-alt me-1"></i>Lihat Full Size
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="remove_payment_proof" 
                                                   name="remove_payment_proof" 
                                                   value="1">
                                            <label class="form-check-label small text-danger" for="remove_payment_proof">
                                                Hapus
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Upload New -->
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
                            @if($order->payment_proof)
                                <br><i class="fas fa-arrow-up me-1"></i>Upload file baru untuk mengganti bukti pembayaran yang ada
                            @endif
                        </small>
                        
                        <!-- New Image Preview -->
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
                        <h4 class="mb-0 text-primary" id="totalPrice">{{ $order->formatted_total_price }}</h4>
                        <small class="text-muted" id="totalItems">{{ $order->orderItems->sum('quantity') }} item</small>
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
                    <!-- Existing items will be loaded here -->
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Pesanan
                        </button>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a>
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
                <input type="text" class="form-control text-right-input" name="items[0][text_right]" maxlength="255">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Teks Kiri (Opsional)</label>
                <input type="text" class="form-control text-left-input" name="items[0][text_left]" maxlength="255">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Teks Tunggal (Opsional)</label>
                <input type="text" class="form-control text-single-input" name="items[0][text_single]" maxlength="255">
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
                    <div class="form-check mt-2 existing-logo-remove-checkbox" style="display: none;">
                        <input class="form-check-input" type="checkbox" name="items[0][remove_logo_path]" value="1">
                        <label class="form-check-label small text-danger">Hapus logo yang sudah ada</label>
                    </div>
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

// Existing order items data - FIXED JSON ENCODING
const existingItems = {!! json_encode($order->orderItems->map(function($item) {
    return [
        'id' => $item->id,
        'product_id' => $item->product_id,
        'quantity' => $item->quantity,
        'material_id' => $item->material_id,
        'material_color_id' => $item->material_color_id,
        'sash_type_id' => $item->sash_type_id,
        'font_id' => $item->font_id,
        'side_motif_id' => $item->side_motif_id,
        'ribbon_color_id' => $item->ribbon_color_id,
        'lace_option_id' => $item->lace_option_id,
        'rombe_option_id' => $item->rombe_option_id,
        'motif_ribbon_option_id' => $item->motif_ribbon_option_id,
        'additional_item_option_id' => $item->additional_item_option_id,
        'text_right' => $item->text_right,
        'text_left' => $item->text_left,
        'text_single' => $item->text_single,
        'logo_path' => $item->logo_path ? asset('storage/' . $item->logo_path) : null,
    ];
})->values()) !!};

// Add Item Button
document.getElementById('addItemBtn').addEventListener('click', function() {
    addItem();
});

// Load existing items on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load existing items
    if (existingItems.length > 0) {
        existingItems.forEach(function(itemData) {
            addItem(itemData);
        });
    } else {
        addItem();
    }
    
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

function addItem(data = null) {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    
    // Update item number
    clone.querySelector('.item-number').textContent = itemIndex + 1;
    
    // Add an item ID field to differentiate between existing and new items
    const itemIdInput = document.createElement('input');
    itemIdInput.type = 'hidden';
    itemIdInput.name = `items[${itemIndex}][id]`;
    if (data && data.id) {
        itemIdInput.value = data.id;
    }
    clone.querySelector('.item-card').prepend(itemIdInput);

    // Update all name attributes with correct index
    clone.querySelectorAll('[name^="items[0]"]').forEach(function(input) {
        input.name = input.name.replace('items[0]', `items[${itemIndex}]`);
    });
    
    // Add to container
    document.getElementById('itemsContainer').appendChild(clone);
    
    // Get the newly added item
    const items = document.querySelectorAll('.item-card');
    const newItem = items[items.length - 1];
    
    // Set existing values if editing
    if (data) {
        newItem.querySelector('[name*="[product_id]"]').value = data.product_id || '';
        newItem.querySelector('[name*="[quantity]"]').value = data.quantity || 1;
        newItem.querySelector('[name*="[material_id]"]').value = data.material_id || '';
        newItem.querySelector('[name*="[material_color_id]"]').value = data.material_color_id || '';
        newItem.querySelector('[name*="[sash_type_id]"]').value = data.sash_type_id || '';
        newItem.querySelector('[name*="[font_id]"]').value = data.font_id || '';
        newItem.querySelector('[name*="[side_motif_id]"]').value = data.side_motif_id || '';
        newItem.querySelector('[name*="[ribbon_color_id]"]').value = data.ribbon_color_id || '';
        newItem.querySelector('[name*="[lace_option_id]"]').value = data.lace_option_id || '';
        newItem.querySelector('[name*="[rombe_option_id]"]').value = data.rombe_option_id || '';
        newItem.querySelector('[name*="[motif_ribbon_option_id]"]').value = data.motif_ribbon_option_id || '';
        newItem.querySelector('[name*="[additional_item_option_id]"]').value = data.additional_item_option_id || '';
        newItem.querySelector('.text-right-input').value = data.text_right || '';
        newItem.querySelector('.text-left-input').value = data.text_left || '';
        newItem.querySelector('.text-single-input').value = data.text_single || '';

        // Handle existing logo
        if (data.logo_path) {
            const logoPreviewContainer = newItem.querySelector('.logo-preview');
            const logoPreviewImg = newItem.querySelector('.logo-preview img');
            const removeLogoCheckboxContainer = newItem.querySelector('.existing-logo-remove-checkbox');
            const removeLogoCheckbox = newItem.querySelector('[name*="[remove_logo_path]"]');

            logoPreviewImg.src = data.logo_path;
            logoPreviewContainer.style.display = 'block';
            removeLogoCheckboxContainer.style.display = 'block';

            removeLogoCheckbox.name = `items[${itemIndex}][remove_logo_path]`; // Update name attribute
        }
    }
    
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

    // Calculate initial price
    calculateItemPrice(newItem);
    
    itemIndex++;
    calculateTotalPrice();
}

function setupItemLogo(itemCard) {
    const logoInput = itemCard.querySelector('.item-logo-path');
    const logoPreviewContainer = itemCard.querySelector('.logo-preview');
    const logoPreviewImg = itemCard.querySelector('.logo-preview img');
    const removeLogoBtn = itemCard.querySelector('.remove-item-logo-btn');
    const existingLogoRemoveCheckboxContainer = itemCard.querySelector('.existing-logo-remove-checkbox');
    const existingLogoRemoveCheckbox = itemCard.querySelector('[name*="[remove_logo_path]"]');

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Reset checkbox if new file is uploaded
            if (existingLogoRemoveCheckbox) {
                existingLogoRemoveCheckbox.checked = false;
            }
            if (file.size > 2048000) { // 2MB
                alert('Ukuran file logo maksimal 2MB!');
                this.value = '';
                logoPreviewContainer.style.display = 'none';
                if (existingLogoRemoveCheckboxContainer) existingLogoRemoveCheckboxContainer.style.display = 'none';
                return;
            }
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Format file logo harus JPG, JPEG, atau PNG!');
                this.value = '';
                logoPreviewContainer.style.display = 'none';
                if (existingLogoRemoveCheckboxContainer) existingLogoRemoveCheckboxContainer.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreviewImg.src = e.target.result;
                logoPreviewContainer.style.display = 'block';
                if (existingLogoRemoveCheckboxContainer) existingLogoRemoveCheckboxContainer.style.display = 'none'; // Hide remove existing option if new logo uploaded
            };
            reader.readAsDataURL(file);
        } else {
            // If no file selected, check if there was an existing logo
            const existingLogoPath = logoPreviewImg.dataset.existingPath; // Assuming you store existing path in data-attribute
            if (existingLogoPath) {
                logoPreviewImg.src = existingLogoPath;
                logoPreviewContainer.style.display = 'block';
                if (existingLogoRemoveCheckboxContainer) existingLogoRemoveCheckboxContainer.style.display = 'block';
            } else {
                logoPreviewContainer.style.display = 'none';
                logoPreviewImg.src = '';
                if (existingLogoRemoveCheckboxContainer) existingLogoRemoveCheckboxContainer.style.display = 'none';
            }
        }
    });

    removeLogoBtn.addEventListener('click', function() {
        logoInput.value = '';
        logoPreviewImg.src = '';
        logoPreviewContainer.style.display = 'none';
        if (existingLogoRemoveCheckboxContainer) {
            existingLogoRemoveCheckboxContainer.style.display = 'block';
            existingLogoRemoveCheckbox.checked = true; // Mark for removal
        }
    });

    // If there's an existing logo, store its path and show the remove checkbox
    if (logoPreviewImg.src) {
        logoPreviewImg.dataset.existingPath = logoPreviewImg.src;
        if (existingLogoRemoveCheckboxContainer) existingLogoRemoveCheckboxContainer.style.display = 'block';
    }
}

function calculateItemPrice(itemCard) {
    let price = 0;
    
    // Product price
    const productSelect = itemCard.querySelector('.product-select');
    if (productSelect.value) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        price += parseFloat(selectedOption.dataset.price || 0);
    }
    
    // All customization prices
    itemCard.querySelectorAll('.customization-select').forEach(function(select) {
        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            price += parseFloat(selectedOption.dataset.price || 0);
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

// Image preview
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

// Form submit
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});
</script>
@endpush