<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Transaction;

abstract class TransactionRequest extends FormRequest
{
    abstract protected function isIncoming(): bool;
    
    public function authorize()
    {
        if ($this->route('transaction')) {
            // Untuk update
            return $this->user()->can('update', $this->route('transaction'));
        }
        // Untuk create
        return $this->user()->can('create', Transaction::class);
    }
    
    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price_at_transaction' => ['required', 'numeric', 'min:0'],
        ];
        
        if ($this->isIncoming()) {
            $rules = array_merge($rules, $this->incomingRules());
        } else {
            $rules = array_merge($rules, $this->outgoingRules());
        }
        
        return $rules;
    }
    
    abstract protected function incomingRules(): array;
    abstract protected function outgoingRules(): array;
}