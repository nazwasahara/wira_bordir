<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AdditionalItem;
use App\Models\AdditionalItemOption;
use App\Models\LaceOption;
use App\Models\Material;
use App\Models\MaterialColor;
use App\Models\MotifRibbonOption;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Models\RibbonColor;
use App\Models\RombeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $query = PurchaseInvoice::with('details');

        // Search
        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                ->orWhereDate('invoice_date', 'like', "%{$search}%");
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('invoice_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('invoice_date', '<=', $dateTo);
        }

        // Sort
        $query->orderBy($sortBy, $sortOrder);

        $invoices = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total_invoices' => PurchaseInvoice::count(),
            'this_month' => PurchaseInvoice::whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->count(),
            'total_value' => PurchaseInvoice::all()->sum('total_amount'),
            'total_items' => PurchaseInvoiceDetail::count(),
        ];

        return view('owner.purchase-invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create()
    {
        // Get all available items
        $materials = Material::all();
        $materialColors = MaterialColor::all();
        $ribbonColors = RibbonColor::all();
        $laceOptions = LaceOption::all();
        $rombeOptions = RombeOption::all();
        $motifRibbonOptions = MotifRibbonOption::all();
        $additionalItems = AdditionalItem::all();
        $additionalItemOptions = AdditionalItemOption::with('additionalItem')->get();

        return view('owner.purchase-invoices.create', compact(
            'materials',
            'materialColors',
            'ribbonColors',
            'laceOptions',
            'rombeOptions',
            'motifRibbonOptions',
            'additionalItems',
            'additionalItemOptions'
        ));
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:' . implode(',', array_keys(PurchaseInvoiceDetail::getItemTypes())),
            'items.*.item_id' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ], [
            'invoice_date. required' => 'Tanggal invoice wajib diisi',
            'items.required' => 'Minimal harus ada 1 item',
            'items.*.item_type.required' => 'Tipe item wajib dipilih',
            'items. *.item_id.required' => 'Item wajib dipilih',
            'items.*.quantity.required' => 'Quantity wajib diisi',
            'items.*.quantity.min' => 'Quantity minimal 1',
            'items.*. unit_price.required' => 'Harga satuan wajib diisi',
            'items.*. unit_price.numeric' => 'Harga satuan harus berupa angka',
            'items.*.unit_price.min' => 'Harga satuan minimal 0',
        ]);

        try {
            DB::beginTransaction();

            // Create invoice
            $invoice = PurchaseInvoice::create([
                'invoice_date' => $validated['invoice_date'],
            ]);

            // Create invoice details
            foreach ($validated['items'] as $itemData) {
                PurchaseInvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => $itemData['item_type'],
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('owner.purchase-invoices.show', $invoice)
                ->with('success', 'Invoice pembelian berhasil dibuat! ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load('details');

        return view('owner.purchase-invoices.show', compact('purchaseInvoice'));
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load('details');

        // Get all available items
        $materials = Material::all();
        $materialColors = MaterialColor::all();
        $ribbonColors = RibbonColor::all();
        $laceOptions = LaceOption::all();
        $rombeOptions = RombeOption::all();
        $motifRibbonOptions = MotifRibbonOption::all();
        $additionalItems = AdditionalItem::all();
        $additionalItemOptions = AdditionalItemOption::with('additionalItem')->get();

        return view('owner.purchase-invoices.edit', compact(
            'purchaseInvoice',
            'materials',
            'materialColors',
            'ribbonColors',
            'laceOptions',
            'rombeOptions',
            'motifRibbonOptions',
            'additionalItems',
            'additionalItemOptions'
        ));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:' . implode(',', array_keys(PurchaseInvoiceDetail::getItemTypes())),
            'items.*.item_id' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ], [
            'invoice_date.required' => 'Tanggal invoice wajib diisi',
            'items.required' => 'Minimal harus ada 1 item',
            'items.*.item_type.required' => 'Tipe item wajib dipilih',
            'items.*.item_id.required' => 'Item wajib dipilih',
            'items.*.quantity.required' => 'Quantity wajib diisi',
            'items.*.quantity.min' => 'Quantity minimal 1',
            'items.*.unit_price.required' => 'Harga satuan wajib diisi',
            'items.*.unit_price.numeric' => 'Harga satuan harus berupa angka',
            'items.*.unit_price.min' => 'Harga satuan minimal 0',
        ]);

        try {
            DB::beginTransaction();

            // Update invoice
            $purchaseInvoice->update([
                'invoice_date' => $validated['invoice_date'],
            ]);

            // Delete old details
            $purchaseInvoice->details()->delete();

            // Create new details
            foreach ($validated['items'] as $itemData) {
                PurchaseInvoiceDetail::create([
                    'invoice_id' => $purchaseInvoice->id,
                    'item_type' => $itemData['item_type'],
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('owner.purchase-invoices.show', $purchaseInvoice)
                ->with('success', 'Invoice pembelian berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        try {
            $invoiceNumber = $purchaseInvoice->invoice_number;

            // Details will be auto-deleted via cascadeOnDelete
            $purchaseInvoice->delete();

            return redirect()
                ->route('owner.purchase-invoices.index')
                ->with('success', "Invoice {$invoiceNumber} berhasil dihapus!");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' .  $e->getMessage());
        }
    }

    /**
     * Get items by type (AJAX)
     */
    public function getItemsByType(Request $request)
    {
        $type = $request->get('type');

        $items = match ($type) {
            // Materials - has 'name' column ✅
            'material' => Material::all(['id', 'name', 'price']),

            // Material Colors - has 'name' column ✅
            'material_color' => MaterialColor::all(['id', 'name', 'price']),

            // Ribbon Colors - has 'name' column ✅
            'ribbon_color' => RibbonColor::all(['id', 'name', 'price']),

            // Lace Options - has 'color' and 'size' (NOT 'name') ⚠️
            'lace' => LaceOption::all(['id', 'color', 'size', 'price'])->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => "{$item->color} - {$item->size}", // Combine color + size
                    'price' => $item->price
                ];
            }),

            // Rombe Options - has 'color' and 'size' (NOT 'name') ⚠️
            'rombe' => RombeOption::all(['id', 'color', 'size', 'price'])->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => "{$item->color} - {$item->size}",
                    'price' => $item->price
                ];
            }),

            'motif_ribbon' => MotifRibbonOption::all(['id', 'color', 'size', 'price'])->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => "{$item->color} - {$item->size}",
                    'price' => $item->price
                ];
            }),

            'additional_item' => AdditionalItem::all(['id', 'name', 'price']),

            'additional_item_option' => AdditionalItemOption::with('additionalItem:id,name')
                ->get(['id', 'additional_item_id', 'color', 'model', 'price'])
                ->map(function ($item) {
                    $additionalItemName = $item->additionalItem ?  $item->additionalItem->name : 'Unknown';
                    return [
                        'id' => $item->id,
                        'name' => "{$additionalItemName} - {$item->color} - {$item->model}", // Combine all info
                        'price' => $item->price
                    ];
                }),

            default => [],
        };

        return response()->json($items);
    }
}
