<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if (!$user->isAdmin()) {
            return $user->id === $model->id; // User hanya bisa update diri sendiri
        }
        
        if ($user->id === $model->id) {
            return !request()->has('role') || request('role') === 'admin';
        }
        
        return true; 
    }

    public function delete(User $user, User $model): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }
        
        if ($user->id === $model->id) {
            return false;
        }
        
        if ($model->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            return $adminCount > 1; 
        }
        
        return true;
    }

    public function updateStatus(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    public function approveSupplier(User $user, User $model): bool
    {
        return $user->isAdmin() && $model->isSupplier();
    }

    public function changeRole(User $user, User $model): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }
        
        if ($user->id === $model->id) {
            return false;
        }
        
        if ($model->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            
            if (request()->has('role') && request('role') !== 'admin') {
                return $adminCount > 2;
            }
        }
        
        return true;
    }

    private function willLeaveAtLeastOneAdmin(User $model, string $newRole = null): bool
    {
        $adminCount = User::where('role', 'admin')->count();
       
        if ($model->isAdmin()) {
            if ($newRole && $newRole !== 'admin') {
                return $adminCount > 1;
            }
            return $adminCount > 1;
        }
        
        return true;
    }
}