<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RibbonColor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RibbonColorController extends Controller
{
    public function index(Request $request)
    {
        $query = RibbonColor::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $ribbonColors = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => RibbonColor::count(),
            'used_in_orders' => RibbonColor::has('orderItems')->count(),
            'total_value' => RibbonColor::sum('price'),
            'average_price' => RibbonColor::avg('price'),
        ];

        return view('services.ribbon-colors.index', compact('ribbonColors', 'stats'));
    }

    /**
     * Show the form for creating a new ribbon color.
     */
    public function create()
    {
        return view('services.ribbon-colors.create');
    }

    /**
     * Store a newly created ribbon color.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ribbon_colors,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama warna pita wajib diisi',
            'name.unique' => 'Nama warna pita sudah ada',
            'price.required' => 'Harga warna pita wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            RibbonColor::create($validated);

            return redirect()
                ->route('services.ribbon-colors.index')
                ->with('success', 'Warna pita berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ribbon color.
     */
    public function show(RibbonColor $ribbonColor)
    {
        // Statistics for this ribbon color
        $stats = [
            'orders_count' => $ribbonColor->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $ribbonColor->orderItems()->sum('quantity'),
            'revenue' => $ribbonColor->orderItems()->sum('final_price'),
        ];

        return view('services.ribbon-colors.show', compact('ribbonColor', 'stats'));
    }

    /**
     * Show the form for editing the specified ribbon color.
     */
    public function edit(RibbonColor $ribbonColor)
    {
        return view('services.ribbon-colors.edit', compact('ribbonColor'));
    }

    /**
     * Update the specified ribbon color.
     */
    public function update(Request $request, RibbonColor $ribbonColor)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ribbon_colors', 'name')->ignore($ribbonColor->id)
            ],
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama warna pita wajib diisi',
            'name.unique' => 'Nama warna pita sudah ada',
            'price.required' => 'Harga warna pita wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $ribbonColor->update($validated);

            return redirect()
                ->route('services.ribbon-colors.index')
                ->with('success', 'Warna pita berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ribbon color.
     */
    public function destroy(RibbonColor $ribbonColor)
    {
        try {
            // Check if ribbon color is used in orders
            if ($ribbonColor->isUsedInOrders()) {
                return back()->with('error', 'Warna pita tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $ribbonColor->delete();

            return redirect()
                ->route('services.ribbon-colors.index')
                ->with('success', 'Warna pita berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
