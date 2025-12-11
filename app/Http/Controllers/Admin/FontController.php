<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Font;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FontController extends Controller
{
    public function index(Request $request)
    {
        $query = Font::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $fonts = $query->paginate(12)->withQueryString();

        // Statistics
        $stats = [
            'total' => Font::count(),
            'used_in_orders' => Font::has('orderItems')->count(),
            'total_value' => Font::sum('price'),
            'average_price' => Font::avg('price'),
        ];

        return view('services.fonts.index', compact('fonts', 'stats'));
    }

    /**
     * Show the form for creating a new font.
     */
    public function create()
    {
        return view('services.fonts.create');
    }

    /**
     * Store a newly created font.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fonts,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama font wajib diisi',
            'name.unique' => 'Nama font sudah ada',
            'price.required' => 'Harga font wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            Font::create($validated);

            return redirect()
                ->route('services.fonts.index')
                ->with('success', 'Font berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified font.
     */
    public function show(Font $font)
    {
        // Statistics for this font
        $stats = [
            'orders_count' => $font->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $font->orderItems()->sum('quantity'),
            'revenue' => $font->orderItems()->sum('final_price'),
        ];

        return view('services.fonts.show', compact('font', 'stats'));
    }

    /**
     * Show the form for editing the specified font.
     */
    public function edit(Font $font)
    {
        return view('services.fonts.edit', compact('font'));
    }

    /**
     * Update the specified font.
     */
    public function update(Request $request, Font $font)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fonts', 'name')->ignore($font->id)
            ],
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama font wajib diisi',
            'name.unique' => 'Nama font sudah ada',
            'price.required' => 'Harga font wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $font->update($validated);

            return redirect()
                ->route('services.fonts.index')
                ->with('success', 'Font berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified font.
     */
    public function destroy(Font $font)
    {
        try {
            // Check if font is used in orders
            if ($font->isUsedInOrders()) {
                return back()->with('error', 'Font tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $font->delete();

            return redirect()
                ->route('services.fonts.index')
                ->with('success', 'Font berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
