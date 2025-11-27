<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\ProductStoreRequest; // Untuk Store
use App\Http\Requests\ProductUpdateRequest; // Untuk Update (Jika berbeda)
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
        
        // Otorisasi: Hanya Admin dan Manager yang boleh mengakses modul ini
        $this->middleware(['auth', 'role:admin|manager']); 
    }

    /**
     * Menampilkan daftar produk dengan filter, search, dan pagination.
     */
    public function index(Request $request)
    {
        // Logika filtering dan search harus ada di ProductService
        $products = $this->service->listProducts(
            $request->get('search'),
            $request->get('category'),
            $request->get('stock_status')
        );
        
        // Memuat kategori untuk filter dropdown
        $categories = \App\Models\Category::all(); 
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan form pembuatan produk baru.
     */
    public function create()
    {
        $categories = \App\Models\Category::all(); // Ambil data kategori
        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(ProductStoreRequest $request)
    {
        // Validasi sudah dilakukan oleh ProductStoreRequest
        
        // Data yang masuk harus menyertakan SKU dan Location
        $data = $request->validated();
        
        // Default stock saat produk baru dibuat (jika tidak ada input awal)
        $data['stock'] = 0; 

        $this->service->createProduct($data);

        return redirect()->route('products.index')
                         ->with('success', 'Produk baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk.
     */
    public function show(Product $product)
    {
        // Riwayat transaksi (5 transaksi terakhir)
        $transactions = $product->transactionDetails()
                                ->with('transaction') // Asumsi relasi ke tabel header
                                ->latest()
                                ->take(5)
                                ->get();
                                
        return view('products.show', compact('product', 'transactions'));
    }

    /**
     * Menampilkan form edit produk.
     */
    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk yang sudah ada.
     */
    public function update(ProductStoreRequest $request, Product $product) 
    {
        // Validasi dan otorisasi di handle oleh ProductStoreRequest

        // SKU tidak boleh diubah, dan stok saat ini juga tidak boleh diubah via update ini
        $data = $request->validated();
        
        // Pastikan SKU tidak ikut terupdate jika menggunakan satu Request (opsional)
        unset($data['sku']); 
        
        $this->service->updateProduct($product, $data);

        return redirect()->route('products.index')
                         ->with('success', 'Data produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Product $product)
    {
        // LOGIKA KRITIS: Pengecekan stok (sebelum memanggil Service)
        if ($product->stock > 0) {
            return back()->with('error', 'Gagal menghapus! Produk masih memiliki stok (' . $product->stock . ' ' . $product->unit . ').');
        }
        
        // Delegasi penghapusan ke Service
        $this->service->deleteProduct($product);

        return redirect()->route('products.index')
                         ->with('success', 'Produk berhasil dihapus.');
    }
}