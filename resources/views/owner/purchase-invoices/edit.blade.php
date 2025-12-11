@extends('layouts.owner')

@section('title', 'Edit Invoice Pembelian')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="fas fa-edit me-2 text-warning"></i>Edit Invoice Pembelian</h2>
        <p class="text-muted mb-0">{{ $purchaseInvoice->invoice_number }}</p>
    </div>
    <a href="{{ route('owner.purchase-invoices.show', $purchaseInvoice) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<form action="{{ route('owner.purchase-invoices.update', $purchaseInvoice) }}" method="POST" id="invoiceForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <!-- Invoice Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Invoice</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="invoice_date" class="form-label">Tanggal Invoice <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('invoice_date') is-invalid @enderror" 
                                   id="invoice_date" 
                                   name="invoice_date" 
                                   value="{{ old('invoice_date', $purchaseInvoice->invoice_date->format('Y-m-d')) }}" 
                                   required>
                            @error('invoice_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Item Pembelian</h6>
                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                        <i class="fas fa-plus me-2"></i>Tambah Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="itemsContainer">
                        <!-- Items will be added here dynamically -->
                    </div>
                    
                    @error('items')
                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr>
                                <td>Total Item:</td>
                                <td class="text-end"><strong id="totalItems">0</strong></td>
                            </tr>
                            <tr>
                                <td>Total Quantity:</td>
                                <td class="text-end"><strong id="totalQty">0</strong></td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Total Nilai:</strong></td>
                                <td class="text-end">
                                    <h5 class="mb-0 text-success" id="grandTotal">Rp 0</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-save me-2"></i>Update Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let itemIndex = 0;
const itemTypes = @json(App\Models\PurchaseInvoiceDetail::getItemTypes());
const existingItems = @json($purchaseInvoice->details);

// Add new item row
document.getElementById('addItemBtn').addEventListener('click', function() {
    addItemRow();
});

function addItemRow(data = null) {
    const container = document.getElementById('itemsContainer');
    const index = itemIndex++;
    
    const itemHtml = `
        <div class="item-row border rounded p-3 mb-3" data-index="${index}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">Item #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tipe Item <span class="text-danger">*</span></label>
                    <select class="form-select item-type" name="items[${index}][item_type]" required>
                        <option value="">Pilih Tipe</option>
                        ${Object.entries(itemTypes).map(([key, label]) => 
                            `<option value="${key}" ${data && data.item_type === key ? 'selected' : ''}>${label}</option>`
                        ).join('')}
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Item <span class="text-danger">*</span></label>
                    <select class="form-select item-select" name="items[${index}][item_id]" required ${data ? '' : 'disabled'}>
                        <option value="">Loading...</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control item-quantity" 
                           name="items[${index}][quantity]" 
                           min="1" 
                           value="${data ? data.quantity : 1}" 
                           required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control item-price" 
                           name="items[${index}][unit_price]" 
                           min="0" 
                           step="0.01" 
                           value="${data ? data.unit_price : ''}" 
                           placeholder="0"
                           required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Subtotal</label>
                    <input type="text" 
                           class="form-control item-subtotal" 
                           readonly 
                           value="Rp 0">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHtml);
    const newItemRow = container.lastElementChild;
    attachItemEvents(newItemRow);
    
    // Load existing data if provided
    if (data) {
        loadItemData(newItemRow, data);
    } else {
        // Update totals for new empty item
        updateTotals();
    }
}

// Load existing item data
function loadItemData(itemRow, data) {
    const itemSelect = itemRow.querySelector('.item-select');
    
    // Fetch items for the selected type
    fetch(`{{ route('owner.purchase-invoices.get-items') }}?type=${data.item_type}`)
        .then(response => response.json())
        .then(items => {
            itemSelect.innerHTML = '<option value="">Pilih Item</option>';
            
            items.forEach(item => {
                const selected = item.id == data.item_id ? 'selected' : '';
                // Hanya tampilkan nama tanpa harga
                itemSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.name}</option>`;
            });
            
            itemSelect.removeAttribute('disabled');
            
            // Calculate subtotal for loaded data
            calculateSubtotal(itemRow);
        })
        .catch(error => {
            console.error('Error loading items:', error);
            itemSelect.innerHTML = '<option value="">Error loading items</option>';
        });
}

function attachItemEvents(itemRow) {
    // Remove item
    itemRow.querySelector('.remove-item').addEventListener('click', function() {
        itemRow.remove();
        updateTotals();
        renumberItems();
    });
    
    // Item type change
    itemRow.querySelector('.item-type').addEventListener('change', function() {
        const itemSelect = itemRow.querySelector('.item-select');
        const selectedType = this.value;
        
        if (selectedType) {
            itemSelect.setAttribute('disabled', 'disabled');
            itemSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`{{ route('owner.purchase-invoices.get-items') }}?type=${selectedType}`)
                .then(response => response.json())
                .then(items => {
                    itemSelect.innerHTML = '<option value="">Pilih Item</option>';
                    
                    if (items.length > 0) {
                        items.forEach(item => {
                            // Hanya tampilkan nama tanpa harga
                            itemSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        itemSelect.removeAttribute('disabled');
                    } else {
                        itemSelect.innerHTML = '<option value="">Tidak ada item</option>';
                        itemSelect.setAttribute('disabled', 'disabled');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    itemSelect.innerHTML = '<option value="">Error</option>';
                    itemSelect.removeAttribute('disabled');
                });
        } else {
            itemSelect.innerHTML = '<option value="">Pilih tipe terlebih dahulu</option>';
            itemSelect.setAttribute('disabled', 'disabled');
        }
    });
    
    // Item select change - tidak autofill harga
    itemRow.querySelector('.item-select').addEventListener('change', function() {
        updateTotals();
    });
    
    // Quantity change
    itemRow.querySelector('.item-quantity').addEventListener('input', function() {
        calculateSubtotal(itemRow);
    });
    
    // Price change
    itemRow.querySelector('.item-price').addEventListener('input', function() {
        calculateSubtotal(itemRow);
    });
}

function calculateSubtotal(itemRow) {
    const quantity = parseFloat(itemRow.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(itemRow.querySelector('.item-price').value) || 0;
    const subtotal = quantity * price;
    
    itemRow.querySelector('.item-subtotal').value = 'Rp ' + numberFormat(subtotal);
    updateTotals();
}

function updateTotals() {
    const items = document.querySelectorAll('.item-row');
    let totalItems = items.length;
    let totalQty = 0;
    let grandTotal = 0;
    
    items.forEach(item => {
        const qty = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        
        totalQty += qty;
        grandTotal += (qty * price);
    });
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('totalQty').textContent = totalQty;
    document.getElementById('grandTotal').textContent = 'Rp ' + numberFormat(grandTotal);
}

function renumberItems() {
    document.querySelectorAll('.item-row').forEach((item, index) => {
        item.querySelector('h6').textContent = `Item #${index + 1}`;
    });
}

function numberFormat(number) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(number);
}

// Load existing items on page load
existingItems.forEach(item => {
    addItemRow(item);
});

// If no existing items, add one empty row
if (existingItems.length === 0) {
    addItemRow();
}
</script>
@endpush