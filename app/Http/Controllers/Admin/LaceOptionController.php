<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaceOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaceOptionController extends Controller
{
    public function index(Request $request)
    {
        $query = LaceOption::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('color', 'like', "%{$search}%");
        }

        // Filter by size
        if ($request->filled('size')) {
            $query->where('size', $request->size);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $laceOptions = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => LaceOption::count(),
            'small' => LaceOption::where('size', LaceOption::SIZE_SMALL)->count(),
            'medium' => LaceOption::where('size', LaceOption::SIZE_MEDIUM)->count(),
            'large' => LaceOption::where('size', LaceOption::SIZE_LARGE)->count(),
            'used_in_orders' => LaceOption::has('orderItems')->count(),
            'total_value' => LaceOption::sum('price'),
        ];

        return view('services.lace-options.index', compact('laceOptions', 'stats'));
    }

    /**
     * Show the form for creating a new lace option.
     */
    public function create()
    {
        $sizes = LaceOption::getSizes();
        return view('services.lace-options.create', compact('sizes'));
    }

    /**
     * Store a newly created lace option.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'size' => ['required', Rule::in(array_keys(LaceOption::getSizes()))],
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna renda wajib diisi',
            'size.required' => 'Ukuran renda wajib dipilih',
            'size.in' => 'Ukuran renda tidak valid',
            'price.required' => 'Harga renda wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            LaceOption::create($validated);

            return redirect()
                ->route('services.lace-options.index')
                ->with('success', 'Opsi renda berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lace option.
     */
    public function show(LaceOption $laceOption)
    {
        // Statistics for this lace option
        $stats = [
            'orders_count' => $laceOption->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $laceOption->orderItems()->sum('quantity'),
            'revenue' => $laceOption->orderItems()->sum('final_price'),
        ];

        return view('services.lace-options.show', compact('laceOption', 'stats'));
    }

    /**
     * Show the form for editing the specified lace option.
     */
    public function edit(LaceOption $laceOption)
    {
        $sizes = LaceOption::getSizes();
        return view('services.lace-options.edit', compact('laceOption', 'sizes'));
    }

    /**
     * Update the specified lace option.
     */
    public function update(Request $request, LaceOption $laceOption)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'size' => ['required', Rule::in(array_keys(LaceOption::getSizes()))],
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna renda wajib diisi',
            'size.required' => 'Ukuran renda wajib dipilih',
            'size.in' => 'Ukuran renda tidak valid',
            'price.required' => 'Harga renda wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $laceOption->update($validated);

            return redirect()
                ->route('services.lace-options.index')
                ->with('success', 'Opsi renda berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lace option.
     */
    public function destroy(LaceOption $laceOption)
    {
        try {
            // Check if lace option is used in orders
            if ($laceOption->isUsedInOrders()) {
                return back()->with('error', 'Opsi renda tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $laceOption->delete();

            return redirect()
                ->route('services.lace-options.index')
                ->with('success', 'Opsi renda berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
