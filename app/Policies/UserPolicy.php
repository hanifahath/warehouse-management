<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin bisa lihat semua
        // User bisa lihat profile sendiri
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Hanya admin yang bisa update user lain
        if (!$user->isAdmin()) {
            return $user->id === $model->id; // User hanya bisa update diri sendiri
        }
        
        // Admin tidak bisa update diri sendiri (kecuali untuk non-role fields)
        if ($user->id === $model->id) {
            // Admin bisa update profile sendiri tapi tidak bisa ubah role
            return !request()->has('role') || request('role') === 'admin';
        }
        
        return true; // Admin bisa update user lain
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Hanya admin yang bisa delete
        if (!$user->isAdmin()) {
            return false;
        }
        
        // Tidak bisa delete diri sendiri
        if ($user->id === $model->id) {
            return false;
        }
        
        // Tidak bisa delete admin lain jika cuma 1 admin tersisa
        if ($model->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            return $adminCount > 1; // Hanya boleh delete jika masih ada admin lain
        }
        
        return true;
    }

    /**
     * Determine whether the user can update user status.
     */
    public function updateStatus(User $user, User $model): bool
    {
        // Hanya admin yang bisa update status
        // Tidak bisa update status diri sendiri
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can approve supplier.
     */
    public function approveSupplier(User $user, User $model): bool
    {
        return $user->isAdmin() && $model->isSupplier();
    }

    /**
     * Determine whether the user can change user role.
     */
    public function changeRole(User $user, User $model): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }
        
        // Tidak bisa ubah role diri sendiri
        if ($user->id === $model->id) {
            return false;
        }
        
        // Tidak bisa ubah admin lain jadi non-admin jika cuma 2 admin tersisa
        if ($model->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            
            // Jika mencoba ubah admin jadi non-admin
            if (request()->has('role') && request('role') !== 'admin') {
                // Minimal harus ada 2 admin sebelum ubah satu admin
                return $adminCount > 2;
            }
        }
        
        return true;
    }
    
    /**
     * Check if at least one admin will remain after operation
     */
    private function willLeaveAtLeastOneAdmin(User $model, string $newRole = null): bool
    {
        $adminCount = User::where('role', 'admin')->count();
        
        // Jika user adalah admin
        if ($model->isAdmin()) {
            // Jika akan diubah jadi non-admin
            if ($newRole && $newRole !== 'admin') {
                return $adminCount > 1;
            }
            // Jika akan didelete
            return $adminCount > 1;
        }
        
        return true;
    }
}