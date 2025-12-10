<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Gunakan Policy untuk authorization
        $category = $this->route('category');
        return $category && $this->user()->can('update', $category);
    }

    public function rules()
    {
        $categoryId = $this->route('category')->id;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $categoryId],
            'description' => ['nullable', 'string'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'], // Tambahkan untuk remove image
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

    public function prepareForValidation()
    {
        // Handle image removal
        if ($this->has('remove_image') && $this->boolean('remove_image')) {
            $this->merge(['image_path' => null]);
        }
    }
}