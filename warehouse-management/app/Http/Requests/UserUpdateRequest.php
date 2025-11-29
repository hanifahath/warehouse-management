<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Admin yang boleh mengubah data user lewat panel ini
        return $this->user() && $this->user()->role === 'Admin';
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Staff', 'Supplier'])],
            'is_approved' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Email sudah terdaftar pada pengguna lain.',
            'role.in' => 'Role tidak valid.',
        ];
    }
}