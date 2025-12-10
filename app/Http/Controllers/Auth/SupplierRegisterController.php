<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierRegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.supplier_register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'company_name' => 'required',
            'company_address' => 'required',
            'phone' => 'required',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'supplier',
            'is_approved' => false,
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'],
            'phone' => $data['phone'],
        ]);

        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil. Menunggu persetujuan admin.');
    }
}
