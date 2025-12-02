<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $authUser)
    {
        return $authUser->role === 'Admin';
    }

    public function view(User $authUser, User $user)
    {
        return $authUser->role === 'Admin' || $authUser->id === $user->id;
    }

    public function update(User $authUser, User $user)
    {
        if ($authUser->role === 'Admin') {
            return $user->role !== 'Admin' || $user->id === $authUser->id;
        }
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user)
    {
        return $authUser->role === 'Admin' && $authUser->id !== $user->id && $user->role !== 'Admin';
    }

    public function approve(User $authUser, User $user)
    {
        return $authUser->role === 'Admin' && $authUser->id !== $user->id && $user->role === 'Supplier';
    }
}
