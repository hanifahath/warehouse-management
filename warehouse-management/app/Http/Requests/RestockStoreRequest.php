<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockStoreRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        return $user && in_array($user->role, ['Manager', 'Admin']);
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
            'items.required' => 'Daftar produk wajib diisi.',
            'items.*.product_id.exists' => 'Produk tidak valid.',
            'items.*.quantity.min' => 'Kuantitas minimal 1.',
        ];
    }
}
