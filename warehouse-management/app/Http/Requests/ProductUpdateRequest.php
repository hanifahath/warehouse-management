<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID produk dari route
        $productId = $this->route('product'); 

        return [
            // SKU harus unik, tetapi abaikan SKU milik produk yang sedang diedit ($productId)
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($productId)],
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'location' => 'nullable|string|max:50',
            'image' => 'nullable|image|max:2048',
        ];
    }
}
