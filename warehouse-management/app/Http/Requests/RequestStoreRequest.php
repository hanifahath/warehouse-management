<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestockStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Warehouse Manager atau Admin yang boleh membuat restock order
        return $this->user() && in_array($this->user()->role, ['Admin', 'Manager']);
    }

    public function rules()
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:users,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'items.required' => 'Daftar item wajib diisi.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'items.*.quantity.min' => 'Kuantitas minimal 1.',
        ];
    }
}