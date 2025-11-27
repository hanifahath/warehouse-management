<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya Admin dan Manager yang boleh membuat/mengedit produk
        return auth()->user()->hasRole(['admin', 'manager']); 
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id ?? null;
        
        return [
            'name' => 'required|string|max:255',
            // SKU harus unik, kecuali saat edit
            'sku' => 'required|string|unique:products,sku,' . $productId, 
            'category_id' => 'required|integer|exists:categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|integer|min:0',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string',
            // stock tidak diinput saat create, di-handle di service jika ada nilai awal
        ];
    }
}