<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalItem;
use App\Models\AdditionalItemOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdditionItemController extends Controller
{
    public function index(Request $request)
    {
        $query = AdditionalItem::with('options');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $additionalItems = $query->paginate(12)->withQueryString();

        // Statistics
        $stats = [
            'total_items' => AdditionalItem::count(),
            'total_options' => AdditionalItemOption::count(),
            'used_in_orders' => AdditionalItem::whereHas('options.orderItems')->count(),
            'total_value' => AdditionalItem::sum('price'),
        ];

        return view('services.additional-items.index', compact('additionalItems', 'stats'));
    }

    /**
     * Show the form for creating a new additional item.
     */
    public function create()
    {
        return view('services.additional-items.create');
    }

    /**
     * Store a newly created additional item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:additional_items,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama item tambahan wajib diisi',
            'name.unique' => 'Nama item tambahan sudah ada',
            'price.required' => 'Harga item tambahan wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            AdditionalItem::create($validated);

            return redirect()
                ->route('services.additional-items.index')
                ->with('success', 'Item tambahan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified additional item.
     */
    public function show(AdditionalItem $additionalItem)
    {
        $additionalItem->load('options');

        // Statistics
        $stats = [
            'options_count' => $additionalItem->options()->count(),
            'orders_count' => $additionalItem->options()->withCount('orderItems')->get()->sum('order_items_count'),
            'revenue' => $additionalItem->options()->withSum('orderItems', 'final_price')->get()->sum('order_items_sum_final_price'),
        ];

        return view('services.additional-items.show', compact('additionalItem', 'stats'));
    }

    /**
     * Show the form for editing the specified additional item.
     */
    public function edit(AdditionalItem $additionalItem)
    {
        return view('services.additional-items.edit', compact('additionalItem'));
    }

    /**
     * Update the specified additional item.
     */
    public function update(Request $request, AdditionalItem $additionalItem)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('additional_items', 'name')->ignore($additionalItem->id)
            ],
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama item tambahan wajib diisi',
            'name.unique' => 'Nama item tambahan sudah ada',
            'price.required' => 'Harga item tambahan wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $additionalItem->update($validated);

            return redirect()
                ->route('services.additional-items.index')
                ->with('success', 'Item tambahan berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified additional item.
     */
    public function destroy(AdditionalItem $additionalItem)
    {
        try {
            // Check if item is used in orders
            if ($additionalItem->isUsedInOrders()) {
                return back()->with('error', 'Item tambahan tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $additionalItem->delete();

            return redirect()
                ->route('services.additional-items.index')
                ->with('success', 'Item tambahan berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store a new option for the additional item.
     */
    public function storeOption(Request $request, AdditionalItem $additionalItem)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna opsi wajib diisi',
            'model.required' => 'Model opsi wajib diisi',
            'price.required' => 'Harga opsi wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $additionalItem->options()->create($validated);

            return back()->with('success', 'Opsi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified option.
     */
    public function updateOption(Request $request, AdditionalItem $additionalItem, AdditionalItemOption $option)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna opsi wajib diisi',
            'model.required' => 'Model opsi wajib diisi',
            'price.required' => 'Harga opsi wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $option->update($validated);

            return back()->with('success', 'Opsi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified option.
     */
    public function destroyOption(AdditionalItem $additionalItem, AdditionalItemOption $option)
    {
        try {
            // Check if option is used in orders
            if ($option->isUsedInOrders()) {
                return back()->with('error', 'Opsi tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $option->delete();

            return back()->with('success', 'Opsi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
