<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // UserController hanya dapat diakses oleh Admin, sudah diamankan di routes/web.php
    
    /**
     * Tampilkan daftar semua pengguna, termasuk filter untuk Supplier yang belum disetujui.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter: Hanya tampilkan Supplier yang belum disetujui
        if ($request->has('filter') && $request->filter === 'unapproved_suppliers') {
            $query->where('role', 'Supplier')
                  ->where('is_approved', false);
        }

        $users = $query->orderBy('name')->paginate(10);
        
        // Data untuk statistik di tampilan (misalnya, di sidebar atau header)
        $unapprovedSuppliersCount = User::where('role', 'Supplier')->where('is_approved', false)->count();

        return view('users.index', compact('users', 'unapprovedSuppliersCount'));
    }

    /**
     * Tampilkan form untuk membuat pengguna baru (Tidak disarankan, lebih baik lewat registrasi)
     */
    public function create()
    {
        // Dalam sistem Warehouse, biasanya user baru (selain Admin) dibuat via registrasi
        // Kita hanya sediakan opsi untuk role yang ada
        $roles = ['Admin', 'Manager', 'Staff', 'Supplier'];
        return view('users.create', compact('roles'));
    }

    /**
     * Simpan pengguna baru (jika Admin ingin membuat akun baru secara manual)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Staff', 'Supplier'])],
            'is_approved' => 'nullable|boolean', // Untuk Supplier, Admin bisa langsung set true
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // Supplier otomatis disetujui jika Admin yang buat (jika tidak ada nilai, default ke false)
            'is_approved' => $request->role === 'Supplier' ? (bool)$request->is_approved : true,
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }
    
    /**
     * Menampilkan detail pengguna tertentu
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Tampilkan form untuk mengedit pengguna
     */
    public function edit(User $user)
    {
        $roles = ['Admin', 'Manager', 'Staff', 'Supplier'];
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Perbarui pengguna
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Staff', 'Supplier'])],
            'is_approved' => 'nullable|boolean',
        ]);
        
        $data = $request->only(['name', 'email', 'role']);
        
        // Jika peran diubah menjadi Supplier, pastikan is_approved diset dengan benar
        if ($request->role === 'Supplier') {
            $data['is_approved'] = (bool)$request->is_approved;
        } else {
            // Jika bukan Supplier, selalu anggap disetujui
            $data['is_approved'] = true;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna
     */
    public function destroy(User $user)
    {
        // Pencegahan: Admin tidak boleh menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Metode khusus untuk Admin menyetujui/menolak Supplier
     * Route: PATCH users/{user}/status
     */
    public function updateStatus(User $user, Request $request)
    {
        // Pastikan hanya Supplier yang bisa diproses di sini
        if ($user->role !== 'Supplier') {
            return back()->with('error', 'Aksi ini hanya berlaku untuk pengguna dengan peran Supplier.');
        }

        // Set is_approved menjadi true (untuk menyetujui) atau false (untuk menolak/membatalkan)
        $newStatus = $request->input('status', 'approve') === 'approve'; // Defaultnya setuju (true)
        
        $user->is_approved = $newStatus;
        $user->save();
        
        $message = $newStatus 
            ? 'Supplier ' . $user->name . ' berhasil disetujui dan kini dapat login.'
            : 'Persetujuan untuk Supplier ' . $user->name . ' dibatalkan.';

        return back()->with('success', $message);
    }
}