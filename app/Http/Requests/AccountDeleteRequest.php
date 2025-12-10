<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountDeleteRequest extends FormRequest
{
    public function authorize()
    {
        // Hanya user yang terautentikasi boleh menghapus akunnya sendiri
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            // Memastikan password yang dikirim adalah password saat ini
            'password' => ['required', 'current_password'],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Password wajib diisi untuk menghapus akun.',
            'password.current_password' => 'Password tidak cocok dengan akun Anda.',
        ];
    }
}