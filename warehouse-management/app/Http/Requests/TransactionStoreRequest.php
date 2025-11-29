<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Product;

class TransactionStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Staff yang boleh membuat transaksi (sesuai requirement)
        return $this->user() && $this->user()->role === 'Staff';
    }

    public function rules()
    {
        // type bisa ditentukan oleh route atau input; di sini kita asumsikan method dipanggil sesuai jenis
        $type = $this->route()->getName() === 'transactions.storeIncoming' ? 'Incoming' : 'Outgoing';
        // atau $this->input('type') jika dikirim

        $base = [
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price_at_transaction' => ['required', 'numeric', 'min:0'],
        ];

        if ($type === 'Incoming') {
            $base['supplier_id'] = ['required', 'integer', 'exists:users,id'];
        } else { // Outgoing
            $base['customer_name'] = ['required', 'string', 'max:255'];
        }

        return $base;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Jika outgoing, cek stok tersedia untuk setiap item
            $routeName = $this->route()->getName();
            $isOutgoing = in_array($routeName, ['transactions.storeOutgoing', 'transactions.outgoing.store']);

            if ($isOutgoing) {
                foreach ($this->input('items', []) as $index => $item) {
                    $product = Product::find($item['product_id'] ?? null);
                    if (!$product) {
                        $validator->errors()->add("items.$index.product_id", 'Produk tidak ditemukan.');
                        continue;
                    }
                    $qty = (int) ($item['quantity'] ?? 0);
                    if ($product->stock < $qty) {
                        $validator->errors()->add("items.$index.quantity", "Stok untuk produk '{$product->name}' tidak mencukupi ({$product->stock}).");
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'items.required' => 'Daftar item wajib diisi.',
            'items.*.product_id.exists' => 'Produk tidak valid.',
            'items.*.quantity.min' => 'Kuantitas minimal 1.',
        ];
    }
}