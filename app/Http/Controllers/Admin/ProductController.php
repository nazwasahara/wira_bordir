<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    use \App\Traits\LogsActivity;

    public function index(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->active();
            } elseif ($request->status == 'inactive') {
                $query->inactive();
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(12)->withQueryString();

        // Statistics
        $stats = [
            'total' => Product::count(),
            'active' => Product::active()->count(),
            'inactive' => Product::inactive()->count(),
            'used_in_orders' => Product::has('orderItems')->count(),
            'total_value' => Product::sum('base_price'),
        ];

        return view('admin.products.index', compact('products', 'stats'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ], [
            'product_name.required' => 'Nama produk wajib diisi',
            'product_name.unique' => 'Nama produk sudah ada',
            'base_price.required' => 'Harga dasar wajib diisi',
            'base_price.numeric' => 'Harga harus berupa angka',
            'base_price.min' => 'Harga minimal 0',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        try {
            $product = Product::create($validated);

            // ✅ CORRECT - Log after create
            self::logActivity(
                action: 'create',
                model: 'Product',
                modelId: $product->id,
                description: "Menambahkan produk '{$product->product_name}' dengan harga Rp " . number_format($product->base_price, 0, ',', '.'),
                oldValues: [],
                newValues: $product->only(['product_name', 'description', 'base_price', 'is_active'])
            );

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Statistics for this product
        $stats = [
            'orders_count' => $product->orderItems()->distinct('order_id')->count('order_id'),
            'total_sold' => $product->orderItems()->sum('quantity'),
            'revenue' => $product->orderItems()->sum('final_price'),
        ];

        return view('admin.products.show', compact('product', 'stats'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'product_name')->ignore($product->id)
            ],
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ], [
            'product_name.required' => 'Nama produk wajib diisi',
            'product_name.unique' => 'Nama produk sudah ada',
            'base_price.required' => 'Harga dasar wajib diisi',
            'base_price.numeric' => 'Harga harus berupa angka',
            'base_price.min' => 'Harga minimal 0',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        try {
            $oldValues = $product->only(['product_name', 'description', 'base_price', 'is_active']);

            $product->update($validated);

            // Get new values after update
            $newValues = $product->only(['product_name', 'description', 'base_price', 'is_active']);

            // Build change description
            $changes = [];
            if ($oldValues['product_name'] != $newValues['product_name']) {
                $changes[] = "nama dari '{$oldValues['product_name']}' ke '{$newValues['product_name']}'";
            }
            if ($oldValues['base_price'] != $newValues['base_price']) {
                $changes[] = "harga dari Rp " . number_format($oldValues['base_price'], 0, ',', '.') .
                    " ke Rp " . number_format($newValues['base_price'], 0, ',', '.');
            }
            if ($oldValues['is_active'] != $newValues['is_active']) {
                $changes[] = "status dari " . ($oldValues['is_active'] ? 'aktif' : 'nonaktif') .
                    " ke " . ($newValues['is_active'] ? 'aktif' : 'nonaktif');
            }

            $changeDescription = !empty($changes)
                ? "Mengubah " . implode(', ', $changes) . " pada produk '{$product->product_name}'"
                : "Memperbarui produk '{$product->product_name}'";

            self::logActivity(
                action: 'update',
                model: 'Product',
                modelId: $product->id,
                description: $changeDescription,
                oldValues: $oldValues,
                newValues: $newValues
            );

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product is used in orders
            if ($product->isUsedInOrders()) {
                return back()->with('error', 'Produk tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $productName = $product->product_name;
            $productData = $product->only(['product_name', 'description', 'base_price', 'is_active']);
            $productId = $product->id;

            $product->delete();

            self::logActivity(
                action: 'delete',
                model: 'Product',
                modelId: $productId,
                description: "Menghapus produk '{$productName}' dengan harga Rp " . number_format($productData['base_price'], 0, ',', '.'),
                oldValues: $productData,
                newValues: []
            );

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Product $product)
    {
        try {
            $oldStatus = $product->is_active;
            $oldValues = [
                'is_active' => $oldStatus,
                'product_name' => $product->product_name
            ];

            // Update status
            $product->update([
                'is_active' => !$product->is_active
            ]);

            $newStatus = $product->is_active;
            $newValues = [
                'is_active' => $newStatus,
                'product_name' => $product->product_name
            ];

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            $fromStatus = $oldStatus ? 'aktif' : 'nonaktif';
            $toStatus = $newStatus ? 'aktif' : 'nonaktif';

            self::logActivity(
                action: 'status_change',
                model: 'Product',
                modelId: $product->id,
                description: "Mengubah status produk '{$product->product_name}' dari {$fromStatus} menjadi {$toStatus}",
                oldValues: $oldValues,
                newValues: $newValues
            );

            return back()->with('success', "Produk berhasil {$statusText}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
