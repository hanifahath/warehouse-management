<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        $restock = $this->route('restock'); // Sesuaikan dengan route binding
        
        if (!$user || !$restock) {
            return false;
        }

        $role = strtolower(trim($user->role ?? ''));
        
        // Only manager/admin can update, and only if status is Pending
        return in_array($role, ['manager', 'admin']) && $restock->status === 'Pending';
    }

    public function rules()
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:users,id'],
            'expected_delivery_date' => ['required', 'date', 'after:order_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'expected_delivery_date.required' => 'Tanggal pengiriman wajib diisi.',
            'items.required' => 'Minimal satu produk harus ditambahkan.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity.min' => 'Kuantitas minimal 1.',
        ];
    }
}