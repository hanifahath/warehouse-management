<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        $transaction = $this->route('transaction');

        if (!$user || !$transaction) {
            return false;
        }

        // Hanya transaksi dengan status Pending boleh di-edit
        if ($transaction->status !== 'Pending') {
            return false;
        }

        // Staff hanya boleh edit transaksi yang mereka buat
        if ($user->role === 'Staff') {
            return $transaction->created_by === $user->id;
        }

        // Admin dan Manager boleh edit
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.price_at_transaction' => 'required_with:items|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'items.required_with' => 'Jika mengubah item, pastikan format item lengkap.',
        ];
    }
}