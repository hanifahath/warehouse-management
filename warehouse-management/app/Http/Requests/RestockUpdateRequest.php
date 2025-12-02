<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        $order = $this->route('restockOrder');
        if (!$user || !$order) return false;

        return in_array($user->role, ['Manager', 'Admin']) && $order->status === 'Pending';
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
}
