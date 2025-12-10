<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockReceiveRequest extends FormRequest
{
    public function authorize()
    {
        // Ubah 'restockOrder' menjadi 'restock' sesuai nama di route
        return $this->user()->can('receive', $this->route('restock'));
    }

    public function rules()
    {
        return [];
    }
}