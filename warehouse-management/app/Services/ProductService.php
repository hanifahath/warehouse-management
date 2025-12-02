<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Mengambil daftar produk berdasarkan filter dan sorting dari request.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getFilteredProducts(Request $request): LengthAwarePaginator
    {
        $query = Product::with('category');

        // 1. Filter Pencarian Nama/SKU
        if ($search = $request->get('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")
                                     ->orWhere('sku', 'like', "%$search%"));
        }

        // 2. Filter Kategori
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 3. Filter Status Stok (Business Logic)
        if ($stockStatus = $request->get('stock_status')) {
            switch ($stockStatus) {
                case 'safe':
                    // Stok aman (Safe): current_stock > min_stock
                    $query->whereColumn('current_stock', '>', 'min_stock');
                    break;
                case 'low':
                    // Stok rendah (Low Stock): current_stock <= min_stock DAN current_stock > 0
                    $query->whereColumn('current_stock', '<=', 'min_stock')
                          ->where('current_stock', '>', 0);
                    break;
                case 'empty':
                    // Stok habis (Out of Stock): current_stock = 0
                    $query->where('current_stock', 0);
                    break;
            }
        }

        // 4. Sorting
        $sort = $request->get('sort', 'latest');
        $query->when($sort === 'oldest', fn($q) => $q->oldest())
              ->when($sort === 'name_asc', fn($q) => $q->orderBy('name', 'asc'))
              ->when($sort === 'name_desc', fn($q) => $q->orderBy('name', 'desc'))
              ->when($sort === 'lowest_stock', fn($q) => $q->orderBy('current_stock', 'asc'))
              ->when($sort === 'highest_stock', fn($q) => $q->orderBy('current_stock', 'desc'))
              ->when($sort === 'latest', fn($q) => $q->latest());

        // 5. Eksekusi Query dan Pagination
        return $query->paginate(10)->withQueryString();
    }

    public function create(array $data): Product
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('product_images', 'public');
        }

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        unset($data['sku']); // SKU tidak boleh diubah

        if (isset($data['image'])) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $data['image']->store('product_images', 'public');
        }

        $product->update($data);
        return $product;
    }

    public function delete(Product $product): bool
    {
        // Mengganti 'stock' dengan 'current_stock' jika itu yang digunakan untuk kolom stok
        if ($product->current_stock > 0) {
            throw new \Exception('Produk tidak bisa dihapus karena stok masih ada.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        return $product->delete();
    }

    public function getCostPrice(Product $product): float
    {
        return $product->cost_price; // Pastikan field cost_price ada di model
    }
}