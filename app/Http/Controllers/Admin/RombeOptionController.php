<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RombeOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RombeOptionController extends Controller
{
    public function index(Request $request)
    {
        $query = RombeOption::query();

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

        $rombeOptions = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => RombeOption::count(),
            'small' => RombeOption::where('size', RombeOption::SIZE_SMALL)->count(),
            'medium' => RombeOption::where('size', RombeOption::SIZE_MEDIUM)->count(),
            'large' => RombeOption::where('size', RombeOption::SIZE_LARGE)->count(),
            'used_in_orders' => RombeOption::has('orderItems')->count(),
            'total_value' => RombeOption::sum('price'),
        ];

        return view('services.rombe-options.index', compact('rombeOptions', 'stats'));
    }

    /**
     * Show the form for creating a new rombe option.
     */
    public function create()
    {
        $sizes = RombeOption::getSizes();
        return view('services.rombe-options.create', compact('sizes'));
    }

    /**
     * Store a newly created rombe option.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'size' => ['required', Rule::in(array_keys(RombeOption::getSizes()))],
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna rombe wajib diisi',
            'size.required' => 'Ukuran rombe wajib dipilih',
            'size.in' => 'Ukuran rombe tidak valid',
            'price.required' => 'Harga rombe wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            RombeOption::create($validated);

            return redirect()
                ->route('services.rombe-options.index')
                ->with('success', 'Opsi rombe berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified rombe option.
     */
    public function show(RombeOption $rombeOption)
    {
        // Statistics for this rombe option
        $stats = [
            'orders_count' => $rombeOption->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $rombeOption->orderItems()->sum('quantity'),
            'revenue' => $rombeOption->orderItems()->sum('final_price'),
        ];

        return view('services.rombe-options.show', compact('rombeOption', 'stats'));
    }

    /**
     * Show the form for editing the specified rombe option.
     */
    public function edit(RombeOption $rombeOption)
    {
        $sizes = RombeOption::getSizes();
        return view('services.rombe-options.edit', compact('rombeOption', 'sizes'));
    }

    /**
     * Update the specified rombe option.
     */
    public function update(Request $request, RombeOption $rombeOption)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'size' => ['required', Rule::in(array_keys(RombeOption::getSizes()))],
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna rombe wajib diisi',
            'size.required' => 'Ukuran rombe wajib dipilih',
            'size.in' => 'Ukuran rombe tidak valid',
            'price.required' => 'Harga rombe wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $rombeOption->update($validated);

            return redirect()
                ->route('services.rombe-options.index')
                ->with('success', 'Opsi rombe berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified rombe option.
     */
    public function destroy(RombeOption $rombeOption)
    {
        try {
            // Check if rombe option is used in orders
            if ($rombeOption->isUsedInOrders()) {
                return back()->with('error', 'Opsi rombe tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $rombeOption->delete();

            return redirect()
                ->route('services.rombe-options.index')
                ->with('success', 'Opsi rombe berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
