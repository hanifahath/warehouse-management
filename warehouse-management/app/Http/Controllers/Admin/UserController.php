<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserStatusUpdateRequest;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->middleware(['auth', 'role:Admin,Manager']);
        $this->service = $service;
    }

    public function index()
    {
        $users = User::orderBy('name')->paginate(10);
        $unapprovedSuppliersCount = User::where('role', 'Supplier')->where('is_approved', false)->count();
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

        $user = $this->service->createUser($request->validated());

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
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

        $user = $this->service->updateUser($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->service->deleteUser($user);

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$user->name} berhasil dihapus.");
    }

    public function updateStatus(User $user, UserStatusUpdateRequest $request)
    {
        $this->authorize('approve', $user);

        $user = $this->service->updateStatus($user, $request->validated()['is_approved']);

        $message = $request->is_approved
            ? "Supplier {$user->name} berhasil disetujui dan kini dapat login."
            : "Persetujuan untuk Supplier {$user->name} dibatalkan. Akun dinonaktifkan.";

        return back()->with('success', $message);
    }
}
