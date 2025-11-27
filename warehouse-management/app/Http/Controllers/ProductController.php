<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Log;

// PASTIKAN ProductController MEWARISI Controller DASAR LARAVEL
class ProductController extends Controller // <--- INI PENTING!
{
    public function __construct()
    {
        // Otorisasi: Hanya Admin dan Manager yang boleh mengakses modul ini
        $this->middleware(['auth', 'role:Admin|Manager']); 
    }

    /**
     * Tampilkan daftar produk (Index).
     */
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    // ... (metode create, store, show, edit, update, destroy lainnya)
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            $product = Product::create($request->validated());
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan produk. Cek log server.');
        }
    }
    
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());
            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui produk. Cek log server.');
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk. Mungkin produk ini terkait dengan transaksi lain.');
        }
    }
}