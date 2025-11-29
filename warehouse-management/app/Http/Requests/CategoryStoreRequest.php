<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Admin atau Manager yang boleh membuat kategori
        return $this->user() && in_array($this->user()->role, ['Admin', 'Manager']);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada. Gunakan nama lain.',
        ];
    }
}