<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestockUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Admin/Manager boleh mengubah PO, dan hanya jika status masih Pending
        $user = $this->user();
        $order = $this->route('restockOrder');

        if (!$user || !$order) {
            return false;
        }

        if (!in_array($user->role, ['Admin', 'Manager'])) {
            return false;
        }

        // Hanya boleh edit jika masih Pending
        return $order->status === 'Pending';
    }

    public function rules()
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:users,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string'],

            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'items.required_with' => 'Jika mengubah item, pastikan format item lengkap.',
        ];
    }
}