<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalItemOption;
use App\Models\CancelledTransaction;
use App\Models\Font;
use App\Models\LaceOption;
use App\Models\Material;
use App\Models\MaterialColor;
use App\Models\MotifRibbonOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RibbonColor;
use App\Models\RombeOption;
use App\Models\SashType;
use App\Models\SideMotif;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use \App\Traits\LogsActivity;

    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone_number', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::pending()->count(),
            'confirmed' => Order::confirmed()->count(),
            'processing' => Order::processing()->count(),
            'completed' => Order::done()->count(),
            'cancelled' => Order::cancelled()->count(),
            'total_revenue' => Order::whereIn('status', ['paid', 'confirm', 'processing', 'done'])->sum('amount_paid'),
            'pending_payment' => Order::pending()->sum('total_price'),
            'revenue_this_month' => DB::selectOne("SELECT fn_get_revenue_this_month() AS revenue")->revenue ?? 0,
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'orderItems.product',
            'orderItems.material',
            'orderItems.materialColor',
            'orderItems.sashType',
            'orderItems.font',
            'orderItems.sideMotif',
            'orderItems.ribbonColor',
            'orderItems.laceOption',
            'orderItems.rombeOption',
            'orderItems.motifRibbonOption',
            'orderItems.additionalItemOption.additionalItem',
            'cancelledTransaction'
        ]);

        // Hitung ulang total menggunakan SQL function untuk validasi
        $calculatedTotal = DB::selectOne("SELECT fn_get_order_total(?) as total", [$order->id])->total ?? 0;
        
        return view('admin.orders.show', compact('order', 'calculatedTotal'));
    }

    /**
     * Update order status - FIXED
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'notes' => 'nullable|string',
        ]);

        try {
            // ✅ CORRECT - Save old status BEFORE update
            $oldStatus = $order->status;
            $oldStatusText = Order::getStatuses()[$oldStatus] ?? $oldStatus;

            $order->update(['status' => $validated['status']]);

            $newStatusText = Order::getStatuses()[$validated['status']] ?? $validated['status'];

            self::logActivity(
                action: 'status_change',
                model: 'Order',
                modelId: $order->id,
                description: "Mengubah status pesanan {$order->order_number} dari '{$oldStatusText}' menjadi '{$newStatusText}'",
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $validated['status']]
            );

            return back()->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Confirm order payment - FIXED
     */
    public function confirmPayment(Order $order)
    {
        try {
            if (!$order->payment_proof) {
                return back()->with('error', 'Tidak ada bukti pembayaran yang diupload!');
            }

            if (!$order->canBeConfirmed()) {
                return back()->with('error', 'Pesanan tidak dapat dikonfirmasi!');
            }

            // ✅ CORRECT - Save old status BEFORE update
            $oldStatus = $order->status;

            $order->update(['status' => Order::STATUS_CONFIRM]);

            self::logActivity(
                action: 'status_change',
                model: 'Order',
                modelId: $order->id,
                description: "Mengonfirmasi pembayaran pesanan {$order->order_number}",
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => Order::STATUS_CONFIRM]
            );

            return back()->with('success', 'Pembayaran berhasil dikonfirmasi!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update payment amount - FIXED
     */
    public function updatePayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
        ], [
            'amount_paid.required' => 'Jumlah pembayaran wajib diisi',
            'amount_paid.numeric' => 'Jumlah pembayaran harus berupa angka',
            'amount_paid.min' => 'Jumlah pembayaran minimal 0',
        ]);

        try {
            // ✅ CORRECT - Save old amount BEFORE update
            $oldAmount = $order->amount_paid;

            $order->update(['amount_paid' => $validated['amount_paid']]);

            self::logActivity(
                action: 'update',
                model: 'Order',
                modelId: $order->id,
                description: "Memperbarui jumlah pembayaran pesanan {$order->order_number} dari Rp " . number_format($oldAmount, 0, ',', '.') . " menjadi Rp " . number_format($validated['amount_paid'], 0, ',', '.'),
                oldValues: ['amount_paid' => $oldAmount],
                newValues: ['amount_paid' => $validated['amount_paid']]
            );

            return back()->with('success', 'Jumlah pembayaran berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order - FIXED
     */
    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan wajib diisi',
        ]);

        try {
            if (!$order->canBeCancelled()) {
                return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah dalam proses!');
            }

            // ✅ CORRECT - Save old status BEFORE update
            $oldStatus = $order->status;

            // Update order status
            $order->update(['status' => Order::STATUS_CANCEL]);

            self::logActivity(
                action: 'status_change',
                model: 'Order',
                modelId: $order->id,
                description: "Membatalkan pesanan {$order->order_number} dengan alasan: {$validated['cancellation_reason']}",
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => Order::STATUS_CANCEL, 'cancellation_reason' => $validated['cancellation_reason']]
            );

            // Create cancelled transaction record
            CancelledTransaction::create([
                'order_id' => $order->id,
                'cancellation_date' => now(),
                'cancellation_reason' => $validated['cancellation_reason'],
            ]);

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Pesanan berhasil dibatalkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete order (only for cancelled orders) - FIXED
     */
    public function destroy(Order $order)
    {
        try {
            if ($order->status !== Order::STATUS_CANCEL) {
                return back()->with('error', 'Hanya pesanan yang dibatalkan yang dapat dihapus!');
            }

            // ✅ CORRECT - Save data BEFORE delete
            $orderNumber = $order->order_number;
            $orderData = [
                'order_number' => $orderNumber,
                'customer_name' => $order->customer_name,
                'total_price' => $order->total_price,
                'status' => $order->status,
            ];
            $orderId = $order->id;

            // Delete payment proof if exists
            if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            // Delete cancelled transaction record
            if ($order->cancelledTransaction) {
                $order->cancelledTransaction->delete();
            }

            // Delete order items
            $order->orderItems()->delete();

            // Delete order
            $order->delete();

            self::logActivity(
                action: 'delete',
                model: 'Order',
                modelId: $orderId,
                description: "Menghapus pesanan {$orderNumber} (Customer: {$orderData['customer_name']}, Total: Rp " . number_format($orderData['total_price'], 0, ',', '.') . ")",
                oldValues: $orderData,
                newValues: []
            );

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'Pesanan berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * View payment proof
     */
    public function viewPaymentProof(Order $order)
    {
        if (!$order->payment_proof || !Storage::disk('public')->exists($order->payment_proof)) {
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        return response()->file(storage_path('app/public/' . $order->payment_proof));
    }

    /**
     * Print order invoice
     */
    public function printInvoice(Order $order)
    {
        $order->load([
            'user',
            'orderItems.product',
            'orderItems.material',
            'orderItems.materialColor',
            'orderItems.sashType',
            'orderItems.font',
            'orderItems.sideMotif',
            'orderItems.ribbonColor',
            'orderItems.laceOption',
            'orderItems.rombeOption',
            'orderItems.motifRibbonOption',
            'orderItems.additionalItemOption.additionalItem'
        ]);

        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $users = User::where('role', 'customer')->where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $materials = Material::all();
        $materialColors = MaterialColor::all();
        $sashTypes = SashType::all();
        $fonts = Font::all();
        $sideMotifs = SideMotif::all();
        $ribbonColors = RibbonColor::all();
        $laceOptions = LaceOption::all();
        $rombeOptions = RombeOption::all();
        $motifRibbonOptions = MotifRibbonOption::all();
        $additionalItemOptions = AdditionalItemOption::with('additionalItem')->get();

        return view('admin.orders.create', compact(
            'users',
            'products',
            'materials',
            'materialColors',
            'sashTypes',
            'fonts',
            'sideMotifs',
            'ribbonColors',
            'laceOptions',
            'rombeOptions',
            'motifRibbonOptions',
            'additionalItemOptions'
        ));
    }

    /**
     * Store a newly created order - FIXED
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone_number' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'payment_proof' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',

            // Order Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.material_id' => 'nullable|exists:materials,id',
            'items.*.material_color_id' => 'nullable|exists:material_colors,id',
            'items.*.sash_type_id' => 'nullable|exists:sash_types,id',
            'items.*.font_id' => 'nullable|exists:fonts,id',
            'items.*.side_motif_id' => 'nullable|exists:side_motifs,id',
            'items.*.ribbon_color_id' => 'nullable|exists:ribbon_colors,id',
            'items.*.lace_option_id' => 'nullable|exists:lace_options,id',
            'items.*.rombe_option_id' => 'nullable|exists:rombe_options,id',
            'items.*.motif_ribbon_option_id' => 'nullable|exists:motif_ribbon_options,id',
            'items.*.additional_item_option_id' => 'nullable|exists:additional_item_options,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Handle payment proof upload
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            // Calculate total price
            $totalPrice = 0;

            // Create order
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'customer_name' => $validated['customer_name'],
                'customer_phone_number' => $validated['customer_phone_number'],
                'customer_address' => $validated['customer_address'],
                'total_price' => 0, // Will update after calculating items
                'amount_paid' => 0,
                'payment_proof' => $paymentProofPath,
                'status' => $validated['status'],
            ]);

            // Create order items and calculate total
            foreach ($validated['items'] as $itemData) {
                $itemPrice = $this->calculateItemPrice($itemData);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'material_id' => $itemData['material_id'] ?? null,
                    'material_color_id' => $itemData['material_color_id'] ?? null,
                    'sash_type_id' => $itemData['sash_type_id'] ?? null,
                    'font_id' => $itemData['font_id'] ?? null,
                    'side_motif_id' => $itemData['side_motif_id'] ?? null,
                    'ribbon_color_id' => $itemData['ribbon_color_id'] ?? null,
                    'lace_option_id' => $itemData['lace_option_id'] ?? null,
                    'rombe_option_id' => $itemData['rombe_option_id'] ?? null,
                    'motif_ribbon_option_id' => $itemData['motif_ribbon_option_id'] ?? null,
                    'additional_item_option_id' => $itemData['additional_item_option_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'final_price' => $itemPrice,
                ]);

                $totalPrice += ($itemPrice * $itemData['quantity']);
            }

            // Update order total price
            $order->update(['total_price' => $totalPrice]);

            // Validasi menggunakan SQL function untuk memastikan konsistensi
            $calculatedTotal = DB::selectOne("SELECT fn_get_order_total(?) as total", [$order->id])->total ?? 0;
            
            // Jika ada perbedaan, update dengan nilai dari function (lebih akurat)
            if (abs($calculatedTotal - $totalPrice) > 0.01) {
                Log::warning("Order {$order->id} total mismatch: calculated={$totalPrice}, function={$calculatedTotal}. Using function value.");
                $order->update(['total_price' => $calculatedTotal]);
                $totalPrice = $calculatedTotal;
            }

            DB::commit();

            // ✅ CORRECT - Log after successful creation
            self::logActivity(
                action: 'create',
                model: 'Order',
                modelId: $order->id,
                description: "Membuat pesanan baru {$order->order_number} untuk customer {$order->customer_name} dengan total Rp " . number_format($totalPrice, 0, ',', '.') . " ({$order->orderItems->count()} item)",
                oldValues: [],
                newValues: [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total_price' => $totalPrice,
                    'items_count' => $order->orderItems->count(),
                    'status' => $order->status
                ]
            );

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if error occurs
            if (isset($paymentProofPath) && Storage::disk('public')->exists($paymentProofPath)) {
                Storage::disk('public')->delete($paymentProofPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        // Only allow edit if order is not paid yet or in processing
        if (in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_DONE])) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Pesanan yang sedang diproses atau selesai tidak dapat diedit!');
        }

        $order->load('orderItems');

        $users = User::where('role', 'customer')->where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $materials = Material::all();
        $materialColors = MaterialColor::all();
        $sashTypes = SashType::all();
        $fonts = Font::all();
        $sideMotifs = SideMotif::all();
        $ribbonColors = RibbonColor::all();
        $laceOptions = LaceOption::all();
        $rombeOptions = RombeOption::all();
        $motifRibbonOptions = MotifRibbonOption::all();
        $additionalItemOptions = AdditionalItemOption::with('additionalItem')->get();

        return view('admin.orders.edit', compact(
            'order',
            'users',
            'products',
            'materials',
            'materialColors',
            'sashTypes',
            'fonts',
            'sideMotifs',
            'ribbonColors',
            'laceOptions',
            'rombeOptions',
            'motifRibbonOptions',
            'additionalItemOptions'
        ));
    }

    /**
     * Update the specified order - FIXED
     */
    public function update(Request $request, Order $order)
    {
        // Only allow update if order is not paid yet or in processing
        if (in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_DONE])) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Pesanan yang sedang diproses atau selesai tidak dapat diedit!');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone_number' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatuses())),
            'payment_proof' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'remove_payment_proof' => 'nullable|boolean',

            // Order Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.material_id' => 'nullable|exists:materials,id',
            'items.*.material_color_id' => 'nullable|exists:material_colors,id',
            'items.*.sash_type_id' => 'nullable|exists:sash_types,id',
            'items.*.font_id' => 'nullable|exists:fonts,id',
            'items.*.side_motif_id' => 'nullable|exists:side_motifs,id',
            'items.*.ribbon_color_id' => 'nullable|exists:ribbon_colors,id',
            'items.*.lace_option_id' => 'nullable|exists:lace_options,id',
            'items.*.rombe_option_id' => 'nullable|exists:rombe_options,id',
            'items.*.motif_ribbon_option_id' => 'nullable|exists:motif_ribbon_options,id',
            'items.*.additional_item_option_id' => 'nullable|exists:additional_item_options,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // ✅ CORRECT - Save old values BEFORE any updates
            $oldValues = [
                'customer_name' => $order->customer_name,
                'customer_phone_number' => $order->customer_phone_number,
                'customer_address' => $order->customer_address,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'items_count' => $order->orderItems->count(),
            ];

            // Handle payment proof
            $paymentProofPath = $order->payment_proof;

            // Remove existing payment proof if requested
            if ($request->has('remove_payment_proof') && $request->remove_payment_proof) {
                if ($paymentProofPath && Storage::disk('public')->exists($paymentProofPath)) {
                    Storage::disk('public')->delete($paymentProofPath);
                }
                $paymentProofPath = null;
            }

            // Upload new payment proof
            if ($request->hasFile('payment_proof')) {
                // Delete old file
                if ($paymentProofPath && Storage::disk('public')->exists($paymentProofPath)) {
                    Storage::disk('public')->delete($paymentProofPath);
                }
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            // Calculate total price
            $totalPrice = 0;

            // Update order info
            $order->update([
                'user_id' => $validated['user_id'],
                'customer_name' => $validated['customer_name'],
                'customer_phone_number' => $validated['customer_phone_number'],
                'customer_address' => $validated['customer_address'],
                'status' => $validated['status'],
                'payment_proof' => $paymentProofPath,
            ]);

            // Delete old items
            $order->orderItems()->delete();

            // Create new order items
            foreach ($validated['items'] as $itemData) {
                $itemPrice = $this->calculateItemPrice($itemData);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'material_id' => $itemData['material_id'] ?? null,
                    'material_color_id' => $itemData['material_color_id'] ?? null,
                    'sash_type_id' => $itemData['sash_type_id'] ?? null,
                    'font_id' => $itemData['font_id'] ?? null,
                    'side_motif_id' => $itemData['side_motif_id'] ?? null,
                    'ribbon_color_id' => $itemData['ribbon_color_id'] ?? null,
                    'lace_option_id' => $itemData['lace_option_id'] ?? null,
                    'rombe_option_id' => $itemData['rombe_option_id'] ?? null,
                    'motif_ribbon_option_id' => $itemData['motif_ribbon_option_id'] ?? null,
                    'additional_item_option_id' => $itemData['additional_item_option_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'final_price' => $itemPrice,
                ]);

                $totalPrice += ($itemPrice * $itemData['quantity']);
            }

            // Update order total price
            $order->update(['total_price' => $totalPrice]);

            // Validasi menggunakan SQL function untuk memastikan konsistensi
            $calculatedTotal = DB::selectOne("SELECT fn_get_order_total(?) as total", [$order->id])->total ?? 0;
            
            // Jika ada perbedaan, update dengan nilai dari function (lebih akurat)
            if (abs($calculatedTotal - $totalPrice) > 0.01) {
                Log::warning("Order {$order->id} total mismatch: calculated={$totalPrice}, function={$calculatedTotal}. Using function value.");
                $order->update(['total_price' => $calculatedTotal]);
                $totalPrice = $calculatedTotal;
            }

            // Refresh to get new data
            $order->refresh();
            $order->load('orderItems');

            DB::commit();

            // ✅ CORRECT - Log with proper old and new values
            $newValues = [
                'customer_name' => $order->customer_name,
                'customer_phone_number' => $order->customer_phone_number,
                'customer_address' => $order->customer_address,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'items_count' => $order->orderItems->count(),
            ];

            self::logActivity(
                action: 'update',
                model: 'Order',
                modelId: $order->id,
                description: "Memperbarui pesanan {$order->order_number} (Total: Rp " . number_format($totalPrice, 0, ',', '.') . ", Items: {$order->orderItems->count()})",
                oldValues: $oldValues,
                newValues: $newValues
            );

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'Pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Calculate item price from all components
     */
    private function calculateItemPrice($itemData)
    {
        $price = 0;

        // Product base price
        if (isset($itemData['product_id'])) {
            $product = Product::find($itemData['product_id']);
            $price += $product ? $product->base_price : 0;
        }

        // Material price
        if (isset($itemData['material_id']) && $itemData['material_id']) {
            $material = Material::find($itemData['material_id']);
            $price += $material ? $material->price : 0;
        }

        // Material Color price
        if (isset($itemData['material_color_id']) && $itemData['material_color_id']) {
            $materialColor = MaterialColor::find($itemData['material_color_id']);
            $price += $materialColor ? $materialColor->price : 0;
        }

        // Sash Type price
        if (isset($itemData['sash_type_id']) && $itemData['sash_type_id']) {
            $sashType = SashType::find($itemData['sash_type_id']);
            $price += $sashType ? $sashType->price : 0;
        }

        // Font price
        if (isset($itemData['font_id']) && $itemData['font_id']) {
            $font = Font::find($itemData['font_id']);
            $price += $font ? $font->price : 0;
        }

        // Side Motif price
        if (isset($itemData['side_motif_id']) && $itemData['side_motif_id']) {
            $sideMotif = SideMotif::find($itemData['side_motif_id']);
            $price += $sideMotif ? $sideMotif->price : 0;
        }

        // Ribbon Color price
        if (isset($itemData['ribbon_color_id']) && $itemData['ribbon_color_id']) {
            $ribbonColor = RibbonColor::find($itemData['ribbon_color_id']);
            $price += $ribbonColor ? $ribbonColor->price : 0;
        }

        // Lace Option price
        if (isset($itemData['lace_option_id']) && $itemData['lace_option_id']) {
            $laceOption = LaceOption::find($itemData['lace_option_id']);
            $price += $laceOption ? $laceOption->price : 0;
        }

        // Rombe Option price
        if (isset($itemData['rombe_option_id']) && $itemData['rombe_option_id']) {
            $rombeOption = RombeOption::find($itemData['rombe_option_id']);
            $price += $rombeOption ? $rombeOption->price : 0;
        }

        // Motif Ribbon Option price
        if (isset($itemData['motif_ribbon_option_id']) && $itemData['motif_ribbon_option_id']) {
            $motifRibbonOption = MotifRibbonOption::find($itemData['motif_ribbon_option_id']);
            $price += $motifRibbonOption ? $motifRibbonOption->price : 0;
        }

        // Additional Item Option price
        if (isset($itemData['additional_item_option_id']) && $itemData['additional_item_option_id']) {
            $additionalItemOption = AdditionalItemOption::find($itemData['additional_item_option_id']);
            $price += $additionalItemOption ? $additionalItemOption->price : 0;
        }

        return $price;
    }

    /**
     * Get revenue by date range using SQL function
     */
    public function getRevenueByDateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:' . implode(',', array_keys(Order::getStatuses())),
        ]);

        try {
            $revenue = DB::selectOne(
                "SELECT fn_get_revenue_by_date_range(?, ?, ?) as revenue",
                [
                    $validated['start_date'],
                    $validated['end_date'],
                    $validated['status'] ?? null
                ]
            )->revenue ?? 0;

            return response()->json([
                'success' => true,
                'revenue' => $revenue,
                'formatted_revenue' => 'Rp ' . number_format($revenue, 0, ',', '.'),
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'] ?? 'all',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate order total using SQL function
     */
    public function recalculateTotal(Order $order)
    {
        try {
            $calculatedTotal = DB::selectOne("SELECT fn_get_order_total(?) as total", [$order->id])->total ?? 0;
            
            $oldTotal = $order->total_price;
            $order->update(['total_price' => $calculatedTotal]);

            self::logActivity(
                action: 'update',
                model: 'Order',
                modelId: $order->id,
                description: "Menghitung ulang total pesanan {$order->order_number} dari Rp " . number_format($oldTotal, 0, ',', '.') . " menjadi Rp " . number_format($calculatedTotal, 0, ',', '.'),
                oldValues: ['total_price' => $oldTotal],
                newValues: ['total_price' => $calculatedTotal]
            );

            return back()->with('success', 'Total pesanan berhasil dihitung ulang!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}