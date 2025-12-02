<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockReceiveRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Manager/Admin/Staff yang bisa menerima
        return $this->user()->can('receive', $this->route('restockOrder'));
    }

    public function rules()
    {
        // Tidak ada input tambahan
        return [];
    }
}
