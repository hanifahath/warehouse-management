<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRejectRequest extends FormRequest
{
    public function authorize()
    {
        $transaction = $this->route('transaction');
        return $transaction && $this->user()->can('reject', $transaction);
    }

    public function rules()
    {
        return [
            'rejection_reason' => 'required|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}