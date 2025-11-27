<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // Otorisasi: Hanya Admin yang boleh mengakses manajemen pengguna
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Menampilkan daftar semua pengguna, dengan fokus pada Supplier yang perlu diapprove.
     */
    public function index(Request $request)
    {
        // Query untuk memfilter pengguna (misalnya, hanya Supplier yang Pending)
        $users = User::query()
            ->when($request->has('role'), function ($query) use ($request) {
                return $query->where('role', $request->role);
            })
            ->when($request->has('status') && $request->status === 'pending', function ($query) {
                // Hanya tampilkan Supplier yang belum disetujui
                return $query->where('role', 'Supplier')->where('is_approved', false);
            })
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan detail pengguna.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * [FUNGSI KRITIS] Menyetujui akun Supplier (mengubah is_approved menjadi true).
     */
    public function approveSupplier(User $user)
    {
        // 1. Pengecekan Otorisasi dan Peran
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Anda tidak memiliki izin untuk melakukan persetujuan.');
        }

        if ($user->role !== 'Supplier') {
            return back()->with('error', 'Hanya akun Supplier yang memerlukan persetujuan.');
        }

        // 2. Cek apakah sudah disetujui
        if ($user->is_approved) {
            return back()->with('warning', 'Supplier ini sudah disetujui sebelumnya.');
        }

        // 3. Update Status
        try {
            $user->update(['is_approved' => true]);
            
            return redirect()->route('users.index')
                             ->with('success', "Akun Supplier '{$user->name}' berhasil disetujui dan kini dapat Login.");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyetujui Supplier: ' . $e->getMessage());
        }
    }
}