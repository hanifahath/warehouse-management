<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Gunakan policy untuk authorization
        $user = $this->route('user');
        return $user && $this->user()->can('update', $user);
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'role' => ['required', Rule::in(['admin', 'manager', 'staff', 'supplier'])],
        ];

        // Jika ada password baru
        if ($this->filled('password')) {
            $rules['password'] = 'sometimes|string|min:8|confirmed';
        }

        // Hanya untuk supplier
        if ($this->role === 'supplier') {
            $rules['is_approved'] = 'sometimes|boolean';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.unique' => 'Email sudah terdaftar pada pengguna lain.',
            'role.in' => 'Role tidak valid.',
        ];
    }

    public function prepareForValidation()
    {
        // Pastikan role lowercase
        if ($this->role) {
            $this->merge(['role' => strtolower($this->role)]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->route('user');
            $currentUser = $this->user();
            
            // Validasi 1: Admin tidak bisa ubah role diri sendiri
            if ($currentUser->id === $user->id && $this->role !== 'admin') {
                $validator->errors()->add('role', 'You cannot change your own role from admin.');
            }
            
            // Validasi 2: Minimal harus ada 1 admin
            if ($user->isAdmin() && $this->role !== 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    $validator->errors()->add('role', 'There must be at least one admin user in the system.');
                }
            }
            
            // Validasi 3: Jika mengubah admin lain, minimal harus ada 2 admin
            if ($user->isAdmin() && $this->role !== 'admin' && $currentUser->id !== $user->id) {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 2) {
                    $validator->errors()->add('role', 'Cannot change admin role. Need at least 2 admins remaining.');
                }
            }
        });
    }
}