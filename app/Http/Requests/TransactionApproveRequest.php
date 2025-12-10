<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionApproveRequest extends FormRequest
{
    public function authorize()
    {
        $transaction = $this->route('transaction');
        return $transaction && $this->user()->can('approve', $transaction);
    }

    public function rules()
    {
        return [
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}