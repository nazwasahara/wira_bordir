<?php

namespace App\Http\Controllers;

use App\Models\Gallery;

class GalleryPageController extends Controller
{
    public function index()
    {
        // Ambil semua gallery dari database, bisa diurutkan terbaru
        $galleries = Gallery::orderBy('created_at', 'desc')->get();

        // Kirim ke blade
        return view('customer.gallery', compact('galleries'));
    }
}
