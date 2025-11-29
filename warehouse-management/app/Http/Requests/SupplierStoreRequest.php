<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Dua opsi umum:
        // 1) Jika pendaftaran publik: kembalikan true (siapa pun boleh register)
        // 2) Jika hanya Admin yang boleh membuat supplier: return $this->user() && $this->user()->role === 'Admin';
        // Pilih sesuai alur aplikasi. Contoh di bawah mengizinkan publik registrasi.
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_approved' => ['sometimes', 'boolean'], // hanya Admin boleh set; frontend biasanya tidak mengirim ini
            // Jika ada file dokumen/sertifikat:
            // 'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama kontak wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalisasi input jika perlu, mis. hapus spasi telepon
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/\s+/', '', $this->input('phone'))]);
        }
    }
}