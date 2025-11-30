<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin|Manager']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filter === 'unapproved_suppliers') {
            $query->where('role', 'Supplier')->where('is_approved', false);
        }

        $users = $query->orderBy('name')->paginate(10);
        $unapprovedSuppliersCount = User::role('Supplier')->where('is_approved', false)->count();

        return view('users.index', compact('users', 'unapprovedSuppliersCount'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = ['Admin', 'Manager', 'Staff', 'Supplier'];
        return view('users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request)
    {
        $this->authorize('create', User::class);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_approved' => $request->role === 'Supplier' ? (bool)$request->is_approved : true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = ['Admin', 'Manager', 'Staff', 'Supplier'];
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->only(['name', 'email', 'role']);
        $data['is_approved'] = $request->role === 'Supplier' ? (bool)$request->is_approved : true;

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function updateStatus(User $user, Request $request)
    {
        $this->authorize('approveSupplier', $user);

        $request->validate(['is_approved' => 'required|boolean']);

        if ($user->role !== 'Supplier') {
            return back()->with('error', 'Aksi ini hanya berlaku untuk Supplier.');
        }

        $user->update(['is_approved' => $request->is_approved]);

        $message = $request->is_approved
            ? "Supplier {$user->name} berhasil disetujui dan kini dapat login."
            : "Persetujuan untuk Supplier {$user->name} dibatalkan. Akun dinonaktifkan.";

        return back()->with('success', $message);
    }
}