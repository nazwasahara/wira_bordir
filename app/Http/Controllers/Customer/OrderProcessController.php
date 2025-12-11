<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CancelledTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Material;
use App\Models\MaterialColor;
use App\Models\SashType;
use App\Models\Font;
use App\Models\SideMotif;
use App\Models\RibbonColor;
use App\Models\LaceOption;
use App\Models\RombeOption;
use App\Models\MotifRibbonOption;
use App\Models\AdditionalItemOption;

class OrderProcessController extends Controller
{

    // STEP 1 — Data Customer
    public function step1()
    {
        $user = Auth::user();
        return view('customer.order.step1', compact('user'));
    }

    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|max:255',
            'customer_phone_number' => 'required|max:20',
            'customer_address' => 'required|max:255',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'customer_name' => $validated['customer_name'],
            'customer_phone_number' => $validated['customer_phone_number'],
            'customer_address' => $validated['customer_address'],
            'total_price' => 0,
        ]);

        return redirect()->route('order.step2', $order->id);
    }


    // STEP 2 — Pilih Selempang
    public function step2($order_id)
    {
        $order = Order::where('id', $order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('customer.order.step2', [
            'order' => $order,
            'products' => Product::where('is_active', 1)->get(),
            'materials' => Material::all(),
            'materialColors' => MaterialColor::all(),
            'sashTypes' => SashType::all(),
            'fonts' => Font::all(),
            'sideMotifs' => SideMotif::all(),

            // Extra Options for Motif
            'ribbonColors' => RibbonColor::all(),
            'laceOptions' => LaceOption::all(),
            'rombeOptions' => RombeOption::all(),
            'motifRibbonOptions' => MotifRibbonOption::all(),

            // Additional Items Options (permata, logo + warna + model)
            'additionalItems' => AdditionalItemOption::all(),
        ]);
    }

    public function saveItems(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            "items" => "required|array|min:1",

            "items.*.product_id" => "required|integer",
            "items.*.material_id" => "required|integer",
            "items.*.material_color_id" => "required|integer",
            "items.*.sash_type_id" => "required|integer",
            "items.*.font_id" => "required|integer",
            "items.*.side_motif_id" => "required|integer",

            "items.*.quantity" => "required|integer|min:1",
            "items.*.final_price" => "required|numeric|min:0",

            "items.*.text_right" => "nullable|string",
            "items.*.text_left" => "nullable|string",
            "items.*.text_single" => "nullable|string",

            "items.*.logo" => "nullable|file|mimes:jpg,jpeg,png,svg|max:2048",

            "items.*.ribbon_color_id" => "nullable|integer",
            "items.*.lace_option_id" => "nullable|integer",
            "items.*.rombe_option_id" => "nullable|integer",
            "items.*.motif_ribbon_option_id" => "nullable|integer",
            "items.*.additional_item_option_id" => "nullable|integer",
        ]);

        $totalSum = 0;

    foreach ($request->items as $index => $item) {

        $logoPath = null;

        if ($request->hasFile("items.$index.logo")) {
            $logoFile = $request->file("items.$index.logo");
            $logoPath = $logoFile->store('order_logos', 'public');
        }

        // Hitung subtotal = final_price per item * qty
        $subtotal = $item['final_price'] * $item['quantity'];
        $totalSum += $subtotal;

        OrderItem::create([
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'material_id' => $item['material_id'],
            'material_color_id' => $item['material_color_id'],
            'sash_type_id' => $item['sash_type_id'],
            'font_id' => $item['font_id'],
            'side_motif_id' => $item['side_motif_id'],

            'ribbon_color_id' => $item['ribbon_color_id'] ?? null,
            'lace_option_id' => $item['lace_option_id'] ?? null,
            'rombe_option_id' => $item['rombe_option_id'] ?? null,
            'motif_ribbon_option_id' => $item['motif_ribbon_option_id'] ?? null,
            'additional_item_option_id' => $item['additional_item_option_id'] ?? null,

            'text_right' => $item['text_right'] ?? null,
            'text_left' => $item['text_left'] ?? null,
            'text_single' => $item['text_single'] ?? null,

            'logo_path' => $logoPath,

            'quantity' => $item['quantity'],
            'final_price' => $item['final_price'],
        ]);
        }

        $order->update([
            'total_price' => $totalSum,
        ]);

        return redirect()->route('order.step3', $order_id);
    }

    // STEP 3 — Review & Konfirmasi
    public function step3($order_id)
    {
        $order = Order::where('id', $order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $items = OrderItem::where('order_id', $order_id)->get();

        return view('customer.order.step3', compact('order', 'items'));
    }

    // Order History
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.order.history', compact('orders'));
    }

    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        // Update order status sesuai enum
        $order->status = 'cancel';
        $order->save();

        // Save cancellation info
        CancelledTransaction::create([
            'order_id' => $order->id,
            'cancellation_date' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()->route('order.history')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function detail(Order $order)
    {
        $items = $order->orderItems()->with([
            'product',
            'material',
            'materialColor',
            'sashType',
            'font',
            'sideMotif',
            'ribbonColor',
            'laceOption',
            'rombeOption',
            'motifRibbonOption',
            'additionalItemOption',
        ])->get();

        $cancelData = null;

        if ($order->status === 'cancel') {
            $cancelData = \App\Models\CancelledTransaction::where('order_id', $order->id)->first();
        }

        return view('customer.order.detail', compact('order', 'items', 'cancelData'));
    }

    public function uploadPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'paid',
        ]);

        return redirect()->route('order.detail', $order->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Tunggu konfirmasi admin.');
    }

}
