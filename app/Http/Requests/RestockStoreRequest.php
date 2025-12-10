<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestockStoreRequest extends FormRequest
{
    public function authorize()
    {
        $user = $this->user();
        return $user && in_array(strtolower($user->role), ['manager', 'admin']);
    }

    public function rules()
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:users,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['required', 'date', 'after:order_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            
            // Format baru: product_id[] dan quantity[]
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'array', 'min:1'],
            'quantity.*' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'order_date.required' => 'Tanggal order wajib diisi.',
            'expected_delivery_date.required' => 'Tanggal pengiriman wajib diisi.',
            'expected_delivery_date.after' => 'Tanggal pengiriman harus setelah tanggal order.',
            'product_id.required' => 'Minimal satu produk harus ditambahkan.',
            'product_id.min' => 'Minimal satu produk harus ditambahkan.',
            'product_id.*.required' => 'Produk wajib dipilih.',
            'product_id.*.exists' => 'Produk tidak valid.',
            'quantity.required' => 'Kuantitas wajib diisi.',
            'quantity.min' => 'Minimal satu produk harus ditambahkan.',
            'quantity.*.required' => 'Kuantitas wajib diisi.',
            'quantity.*.min' => 'Kuantitas minimal 1.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Transform product_id[] & quantity[] to items[] format for service
     */
    protected function prepareForValidation()
    {
        // Debug
        \Log::info('RestockStoreRequest - prepareForValidation');
        \Log::info('Raw input product_id:', $this->input('product_id', []));
        \Log::info('Raw input quantity:', $this->input('quantity', []));
        
        // Transform product_id[] & quantity[] to items[] format
        $productIds = $this->input('product_id', []);
        $quantities = $this->input('quantity', []);
        
        $items = [];
        
        // Check if both arrays have same length
        if (count($productIds) === count($quantities)) {
            for ($i = 0; $i < count($productIds); $i++) {
                if (!empty($productIds[$i]) && !empty($quantities[$i])) {
                    $items[] = [
                        'product_id' => (int) $productIds[$i],
                        'quantity' => (int) $quantities[$i]
                    ];
                }
            }
        }
        
        \Log::info('Transformed items:', $items);
        
        // Merge items into request data
        $this->merge([
            'items' => $items
        ]);
    }
}