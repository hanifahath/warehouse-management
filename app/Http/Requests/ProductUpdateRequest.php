<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && in_array($this->user()->role, ['admin', 'manager']);
    }

    public function rules()
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            // SKU tidak boleh diubah; jika form mengirim sku, tolak perubahan
            // Pastikan front-end tidak mengirim sku; jika tetap dikirim, ignore atau validasi agar sama dengan existing
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'rack_location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Jika front-end mengirim sku yang berbeda, tolak perubahan SKU
            if ($this->has('sku')) {
                $product = $this->route('product');
                if ($product && $this->input('sku') !== $product->sku) {
                    $validator->errors()->add('sku', 'SKU tidak dapat diubah.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}