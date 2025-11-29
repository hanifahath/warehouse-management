<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin|Manager']);
    }

    /**
     * Menampilkan daftar produk dengan dukungan pencarian, filter, dan sorting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 1. Ambil semua kategori untuk filter dropdown
        $categories = Category::all();
        
        // 2. Membangun Query Dasar (Eager loading category)
        $query = Product::with('category');

        // 3. Logika Search (berdasarkan nama atau SKU)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // 4. Logika Filter Kategori
        if ($categoryId = $request->get('category')) {
            $query->where('category_id', $categoryId);
        }

        // 5. Logika Filter Status Stok
        if ($stockStatus = $request->get('stock_status')) {
            if ($stockStatus === 'low') {
                // Stok di bawah batas minimum
                $query->whereColumn('stock', '<', 'min_stock');
            } elseif ($stockStatus === 'ok') {
                // Stok sama dengan atau di atas batas minimum
                $query->whereColumn('stock', '>=', 'min_stock');
            }
        }

        // 6. Logika Sorting
        $sort = $request->get('sort', 'latest'); // Default sort: latest
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'lowest_stock':
                $query->orderBy('stock', 'asc');
                break;
            case 'highest_stock':
                $query->orderBy('stock', 'desc');
                break;
            case 'latest':
            default:
                // Menggunakan created_at secara default untuk latest
                $query->latest(); 
                break;
        }

        // 7. Eksekusi query, Pagination, dan mempertahankan parameter filter (withQueryString)
        $products = $query->paginate(10)->withQueryString();

        // 8. Kirim $products dan $categories ke view
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request)
    {
        $this->authorize('create', Product::class);

        try {
            $validatedData = $request->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('product_images', 'public');
                $validatedData['image'] = $path;
            }

            Product::create($validatedData);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan produk.');
        }
    }

    public function show(Product $product)
    {
        // 1. Memuat relasi category agar tidak ada error di view
        $product->load('category');

        // 2. Mengambil detail transaksi terbaru
        // Memuat 10 transaksi terakhir yang melibatkan produk ini
        $recentTransactions = $product->transactionDetails()
                                    ->with('transaction') // Menggunakan eager loading untuk memuat data Transaction utama
                                    ->latest()
                                    ->take(10)
                                    ->get();

        // 3. Mengirim KEDUA variabel ke view
        // Jika $recentTransactions tidak ada di compact(), error 'Undefined variable' akan muncul di view.
        return view('products.show', compact('product', 'recentTransactions'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        try {
            $validatedData = $request->validated();

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $path = $request->file('image')->store('product_images', 'public');
                $validatedData['image'] = $path;
            }

            $product->update($validatedData);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui produk.');
        }
    }

    public function getCostPrice($id)
    {
        // Cari produk. Gunakan findOrFail untuk mengembalikan 404 jika tidak ditemukan
        $product = Product::findOrFail($id);

        // Kembalikan harga beli produk saat ini sebagai JSON.
        // Asumsi kolom harga beli adalah 'cost_price'.
        return response()->json([
            'id' => $product->id,
            'unit_price' => $product->cost_price, // Pastikan ini sesuai dengan nama kolom di DB Anda
        ]);
    }
    
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        try {
            if ($product->stock > 0) {
                return back()->with('error', 'Produk tidak bisa dihapus karena stok masih ada.');
            }

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk.');
        }
    }
}