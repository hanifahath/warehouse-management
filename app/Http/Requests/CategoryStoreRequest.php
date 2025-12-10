<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Gunakan Policy untuk authorization
        return $this->user()->can('create', \App\Models\Category::class);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'], // Max 2MB
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah ada. Gunakan nama lain.',
            'image_path.image' => 'File harus berupa gambar.',
            'image_path.mimes' => 'Gambar harus berformat: jpeg, png, jpg, gif, svg, atau webp.',
            'image_path.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}