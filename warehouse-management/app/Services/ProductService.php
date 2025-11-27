<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductDetail;

class ProductService
{
    public function listProducts()
    {
        return Product::with('details')->paginate(10);
    }

    public function createProduct(array $data)
    {
        $product = Product::create([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'purchase_price' => $data['purchase_price'],
            'selling_price' => $data['selling_price'],
            'min_stock' => $data['min_stock'],
            'unit' => $data['unit'],
        ]);

        ProductDetail::create([
            'product_id' => $product->id,
            'description' => $data['description'] ?? '-',
            'brand' => $data['brand'] ?? '-',
            'specification' => $data['specification'] ?? '-',
        ]);

        return $product;
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->update($data);

        $product->details->update([
            'description' => $data['description'],
            'brand' => $data['brand'],
            'specification' => $data['specification'],
        ]);

        return $product;
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
    }
}
