<?php

namespace App\Policies;

use App\Models\RestockOrder;
use App\Models\User;

class RestockPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager', 'Staff', 'Supplier']);
    }

    public function view(User $user, RestockOrder $order): bool
    {
        if ($user->role === 'Supplier') {
            return $order->supplier_id === $user->id;
        }
        return in_array($user->role, ['Admin', 'Manager', 'Staff']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    public function update(User $user, RestockOrder $order): bool
    {
        return in_array($user->role, ['Admin', 'Manager']) && $order->status === 'Pending';
    }

    public function updateStatus(User $user, RestockOrder $order, string $status): bool
    {
        if (!in_array($user->role, ['Manager', 'Admin', 'Staff'])) {
            return false;
        }

        $validNext = [
            'Pending' => ['Confirmed by Supplier'],
            'Confirmed by Supplier' => ['In Transit'],
            'In Transit' => ['Received'],
        ];

        $current = $order->status;
        return isset($validNext[$current]) && in_array($status, $validNext[$current]);
    }

    public function confirm(User $user, RestockOrder $order): bool
    {
        return $user->role === 'Supplier' && $order->status === 'Pending';
    }

    public function receive(User $user, RestockOrder $order): bool
    {
        return in_array($user->role, ['Manager', 'Staff']) && $order->status === 'In Transit';
    }
}
