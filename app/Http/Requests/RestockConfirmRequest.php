<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestockConfirmRequest extends FormRequest
{
    public function authorize()
    {
        // Supplier hanya bisa konfirmasi order miliknya
        return $this->user()->can('confirm', $this->route('restockOrder'));
    }

    public function rules()
    {
        // Tidak ada input tambahan, karena hanya aksi konfirmasi
        return [];
    }
}
