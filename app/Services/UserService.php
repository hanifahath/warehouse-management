<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function createUser(array $data): User
    {
        $isApproved = $data['role'] === 'supplier' 
        ? ($data['is_approved'] ?? false) 
        : true;

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_approved' => $data['role'] === 'supplier' ? ($data['is_approved'] ?? false) : true,
        ]);
    }

    // app/Services/UserService.php

    public function updateUser(User $user, array $data): User
    {
        $currentUser = auth()->user();
        
        // Check role change safety
        if (isset($data['role']) && !$user->canChangeRole($data['role'], $currentUser)) {
            if ($user->id === $currentUser->id) {
                throw new \Exception('You cannot change your own role from admin.');
            } elseif ($user->isAdmin()) {
                throw new \Exception('Cannot change admin role. Need at least 2 admins remaining.');
            }
        }
        
        // Handle is_approved untuk non-supplier
        if ($user->role !== 'supplier') {
            $data['is_approved'] = $user->is_approved;
        }
        
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user): void
    {
        $currentUser = auth()->user();
        
        // Check delete safety
        if (!$user->canBeDeleted($currentUser)) {
            if ($user->id === $currentUser->id) {
                throw new \Exception('You cannot delete your own account.');
            } elseif ($user->isAdmin()) {
                throw new \Exception('Cannot delete admin. At least one admin must remain.');
            }
        }
        
        $user->delete();
    }

    public function updateStatus(User $user, bool $isApproved): User
    {
        $user->update(['is_approved' => $isApproved]);
        return $user;
    }

    /**
     * Get filtered and paginated users
     */
    public function getFilteredUsers(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        // Search by name or email
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'pending':
                    $query->where('role', 'supplier')->where('is_approved', false);
                    break;
                case 'approved':
                    $query->where('role', 'supplier')->where('is_approved', true);
                    break;
                case 'active':
                    $query->where('role', '!=', 'supplier');
                    break;
            }
        }

        // Default sorting by name
        $query->orderBy('name');

        // Apply pagination
        return $query->paginate($perPage);
    }

    /**
     * Get counts for each status tab
     */
    public function getUserCounts(): array
    {
        return [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'manager' => User::where('role', 'manager')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'supplier' => User::where('role', 'supplier')->count(),
            'pending' => User::where('role', 'supplier')->where('is_approved', false)->count(),
        ];
    }


}