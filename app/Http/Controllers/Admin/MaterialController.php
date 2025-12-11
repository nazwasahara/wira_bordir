<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('colors');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('colors', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $materials = $query->paginate(10)->withQueryString();

        // Statistics
        $stats = [
            'total' => Material::count(),
            'total_colors' => MaterialColor::count(),
            'used_in_orders' => Material::has('orderItems')->count(),
            'total_value' => Material::sum('price'),
        ];

        return view('services.materials.index', compact('materials', 'stats'));
    }

    /**
     * Show the form for creating a new material.
     */
    public function create()
    {
        return view('services.materials.create');
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:materials,name',
            'price' => 'required|numeric|min:0',
            'colors' => 'nullable|array',
            'colors.*.name' => 'required_with:colors|string|max:255',
            'colors.*.price' => 'required_with:colors|numeric|min:0',
        ], [
            'name.required' => 'Nama material wajib diisi',
            'name.unique' => 'Nama material sudah ada',
            'price.required' => 'Harga material wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
            'colors.*.name.required_with' => 'Nama warna wajib diisi',
            'colors.*.price.required_with' => 'Harga warna wajib diisi',
            'colors.*.price.numeric' => 'Harga warna harus berupa angka',
        ]);

        DB::beginTransaction();
        try {
            // Create material
            $material = Material::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
            ]);

            // Create colors if exists
            if (!empty($validated['colors'])) {
                foreach ($validated['colors'] as $color) {
                    $material->colors()->create([
                        'name' => $color['name'],
                        'price' => $color['price'],
                    ]);
                }
            }

            DB::commit();
            return redirect()
                ->route('services.materials.index')
                ->with('success', 'Material berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified material.
     */
    public function show(Material $material)
    {
        $material->load('colors');

        // Statistics for this material
        $stats = [
            'colors_count' => $material->colors()->count(),
            'orders_count' => $material->orderItems()->distinct('order_id')->count('order_id'),
            'total_sold' => $material->orderItems()->sum('quantity'),
            'revenue' => $material->orderItems()->sum('final_price'),
        ];

        return view('services.materials.show', compact('material', 'stats'));
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Material $material)
    {
        $material->load('colors');
        return view('services.materials.edit', compact('material'));
    }

    /**
     * Update the specified material.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materials', 'name')->ignore($material->id)
            ],
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama material wajib diisi',
            'name.unique' => 'Nama material sudah ada',
            'price.required' => 'Harga material wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $material->update($validated);

            return redirect()
                ->route('services.materials.index')
                ->with('success', 'Material berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified material.
     */
    public function destroy(Material $material)
    {
        try {
            // Check if material is used in orders
            if ($material->isUsedInOrders()) {
                return back()->with('error', 'Material tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            // Delete all colors first
            $material->colors()->delete();

            // Delete material
            $material->delete();

            return redirect()
                ->route('services.materials.index')
                ->with('success', 'Material berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store a new color for material
     */
    public function storeColor(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama warna wajib diisi',
            'price.required' => 'Harga warna wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $material->colors()->create($validated);

            return back()->with('success', 'Warna berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update material color
     */
    public function updateColor(Request $request, Material $material, MaterialColor $color)
    {
        // Check if color belongs to material
        if ($color->material_id !== $material->id) {
            return back()->with('error', 'Warna tidak ditemukan!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama warna wajib diisi',
            'price.required' => 'Harga warna wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga minimal 0',
        ]);

        try {
            $color->update($validated);

            return back()->with('success', 'Warna berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete material color
     */
    public function destroyColor(Material $material, MaterialColor $color)
    {
        // Check if color belongs to material
        if ($color->material_id !== $material->id) {
            return back()->with('error', 'Warna tidak ditemukan!');
        }

        try {
            // Check if color is used in orders
            if ($color->isUsedInOrders()) {
                return back()->with('error', 'Warna tidak dapat dihapus karena sudah digunakan dalam pesanan!');
            }

            $color->delete();

            return back()->with('success', 'Warna berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
