<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SashType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SashTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = SashType::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $sashTypes = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => SashType::count(),
            'used_in_orders' => SashType::has('orderItems')->count(),
            'total_value' => SashType::sum('price'),
            'average_price' => SashType::avg('price'),
        ];

        return view('services.sash-types.index', compact('sashTypes', 'stats'));
    }

    /**
     * Show the form for creating a new sash type.
     */
    public function create()
    {
        return view('services.sash-types.create');
    }

    /**
     * Store a newly created sash type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sash_types,name',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama tipe sash wajib diisi',
            'name.unique' => 'Nama tipe sash sudah ada',
            'price.required' => 'Harga tipe sash wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            SashType::create($validated);

            return redirect()
                ->route('services.sash-types.index')
                ->with('success', 'Tipe sash berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified sash type.
     */
    public function show(SashType $sashType)
    {
        // Statistics for this sash type
        $stats = [
            'orders_count' => $sashType->orderItems()->distinct('order_id')->count('order_id'),
            'total_used' => $sashType->orderItems()->sum('quantity'),
            'revenue' => $sashType->orderItems()->sum('final_price'),
        ];

        return view('services.sash-types.show', compact('sashType', 'stats'));
    }

    /**
     * Show the form for editing the specified sash type.
     */
    public function edit(SashType $sashType)
    {
        return view('services.sash-types.edit', compact('sashType'));
    }

    /**
     * Update the specified sash type.
     */
    public function update(Request $request, SashType $sashType)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sash_types', 'name')->ignore($sashType->id)
            ],
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama tipe sash wajib diisi',
            'name.unique' => 'Nama tipe sash sudah ada',
            'price.required' => 'Harga tipe sash wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $sashType->update($validated);

            return redirect()
                ->route('services.sash-types.index')
                ->with('success', 'Tipe sash berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified sash type.
     */
    public function destroy(SashType $sashType)
    {
        try {
            // Check if sash type is used in orders
            if ($sashType->isUsedInOrders()) {
                return back()->with('error', 'Tipe sash tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $sashType->delete();

            return redirect()
                ->route('services.sash-types.index')
                ->with('success', 'Tipe sash berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
