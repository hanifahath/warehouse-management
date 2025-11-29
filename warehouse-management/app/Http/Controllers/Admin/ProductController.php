<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
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

    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('products.index', compact('products'));
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

            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error storing product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan produk.');
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
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

            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui produk.');
        }
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
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk.');
        }
    }
}