<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya Admin atau supplier itu sendiri yang boleh update profilnya
        $user = $this->user();
        $target = $this->route('user'); // asumsi route model binding user
        if (!$user || !$target) return false;

        return $user->role === 'Admin' || $user->id === $target->id;
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users','email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_approved' => ['sometimes', 'boolean'], // hanya Admin yang boleh mengubah
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}