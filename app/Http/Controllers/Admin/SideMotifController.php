<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SideMotif;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SideMotifController extends Controller
{
    public function index(Request $request)
    {
        $query = SideMotif::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $sideMotifs = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => SideMotif::count(),
            'used_in_orders' => SideMotif::has('orderItems')->count(),
            'total_value' => SideMotif::sum('price'),
            'average_price' => SideMotif::avg('price'),
        ];

        return view('services.side-motifs.index', compact('sideMotifs', 'stats'));
    }

    /**
     * Show the form for creating a new side motif.
     */
    public function create()
    {
        return view('services.side-motifs.create');
    }

    /**
     * Store a newly created side motif.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:side_motifs,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama motif samping wajib diisi',
            'name.unique' => 'Nama motif samping sudah ada',
            'price.required' => 'Harga motif samping wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            SideMotif::create($validated);

            return redirect()
                ->route('services.side-motifs.index')
                ->with('success', 'Motif samping berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified side motif.
     */
    public function show(SideMotif $sideMotif)
    {
        // Statistics for this side motif
        $stats = [
            'orders_count' => $sideMotif->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $sideMotif->orderItems()->sum('quantity'),
            'revenue' => $sideMotif->orderItems()->sum('final_price'),
        ];

        return view('services.side-motifs.show', compact('sideMotif', 'stats'));
    }

    /**
     * Show the form for editing the specified side motif.
     */
    public function edit(SideMotif $sideMotif)
    {
        return view('services.side-motifs.edit', compact('sideMotif'));
    }

    /**
     * Update the specified side motif.
     */
    public function update(Request $request, SideMotif $sideMotif)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('side_motifs', 'name')->ignore($sideMotif->id)
            ],
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama motif samping wajib diisi',
            'name.unique' => 'Nama motif samping sudah ada',
            'price.required' => 'Harga motif samping wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $sideMotif->update($validated);

            return redirect()
                ->route('services.side-motifs.index')
                ->with('success', 'Motif samping berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified side motif.
     */
    public function destroy(SideMotif $sideMotif)
    {
        try {
            // Check if side motif is used in orders
            if ($sideMotif->isUsedInOrders()) {
                return back()->with('error', 'Motif samping tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $sideMotif->delete();

            return redirect()
                ->route('services.side-motifs.index')
                ->with('success', 'Motif samping berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
