<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_approved' => $data['role'] === 'Supplier' ? ($data['is_approved'] ?? false) : true,
        ]);
    }

    public function updateUser(User $user, array $data): User
    {
        if ($user->role !== 'Supplier') {
            $data['is_approved'] = $user->is_approved;
        }

        $user->update($data);
        return $user;
    }

    public function updateStatus(User $user, bool $isApproved): User
    {
        $user->update(['is_approved' => $isApproved]);
        return $user;
    }

    public function deleteUser(User $user)
    {
        $user->delete();
    }
}
