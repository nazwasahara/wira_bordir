<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotifRibbonOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MotifRibbonOptionController extends Controller
{
    public function index(Request $request)
    {
        $query = MotifRibbonOption::query();

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

        $motifRibbonOptions = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => MotifRibbonOption::count(),
            'small' => MotifRibbonOption::where('size', MotifRibbonOption::SIZE_SMALL)->count(),
            'medium' => MotifRibbonOption::where('size', MotifRibbonOption::SIZE_MEDIUM)->count(),
            'large' => MotifRibbonOption::where('size', MotifRibbonOption::SIZE_LARGE)->count(),
            'used_in_orders' => MotifRibbonOption::has('orderItems')->count(),
            'total_value' => MotifRibbonOption::sum('price'),
        ];

        return view('services.motif-ribbon-options.index', compact('motifRibbonOptions', 'stats'));
    }

    /**
     * Show the form for creating a new motif ribbon option.
     */
    public function create()
    {
        $sizes = MotifRibbonOption::getSizes();
        return view('services.motif-ribbon-options.create', compact('sizes'));
    }

    /**
     * Store a newly created motif ribbon option.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'size' => ['required', Rule::in(array_keys(MotifRibbonOption::getSizes()))],
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna pita motif wajib diisi',
            'size.required' => 'Ukuran pita motif wajib dipilih',
            'size.in' => 'Ukuran pita motif tidak valid',
            'price.required' => 'Harga pita motif wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            MotifRibbonOption::create($validated);

            return redirect()
                ->route('services.motif-ribbon-options.index')
                ->with('success', 'Opsi pita motif berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified motif ribbon option.
     */
    public function show(MotifRibbonOption $motifRibbonOption)
    {
        // Statistics for this motif ribbon option
        $stats = [
            'orders_count' => $motifRibbonOption->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $motifRibbonOption->orderItems()->sum('quantity'),
            'revenue' => $motifRibbonOption->orderItems()->sum('final_price'),
        ];

        return view('services.motif-ribbon-options.show', compact('motifRibbonOption', 'stats'));
    }

    /**
     * Show the form for editing the specified motif ribbon option.
     */
    public function edit(MotifRibbonOption $motifRibbonOption)
    {
        $sizes = MotifRibbonOption::getSizes();
        return view('services.motif-ribbon-options.edit', compact('motifRibbonOption', 'sizes'));
    }

    /**
     * Update the specified motif ribbon option.
     */
    public function update(Request $request, MotifRibbonOption $motifRibbonOption)
    {
        $validated = $request->validate([
            'color' => 'required|string|max:255',
            'size' => ['required', Rule::in(array_keys(MotifRibbonOption::getSizes()))],
            'price' => 'required|numeric|min:0',
        ], [
            'color.required' => 'Warna pita motif wajib diisi',
            'size.required' => 'Ukuran pita motif wajib dipilih',
            'size.in' => 'Ukuran pita motif tidak valid',
            'price.required' => 'Harga pita motif wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $motifRibbonOption->update($validated);

            return redirect()
                ->route('services.motif-ribbon-options.index')
                ->with('success', 'Opsi pita motif berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified motif ribbon option.
     */
    public function destroy(MotifRibbonOption $motifRibbonOption)
    {
        try {
            // Check if motif ribbon option is used in orders
            if ($motifRibbonOption->isUsedInOrders()) {
                return back()->with('error', 'Opsi pita motif tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $motifRibbonOption->delete();

            return redirect()
                ->route('services.motif-ribbon-options.index')
                ->with('success', 'Opsi pita motif berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
