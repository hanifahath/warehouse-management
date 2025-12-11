<?php

namespace App\Policies;

use App\Models\RestockOrder;
use App\Models\User;

class RestockOrderPolicy
{

    public function viewAny(User $user): bool
    {
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }
        
        return $user->isSupplier() && $user->is_approved;
    }

    public function view(User $user, RestockOrder $restockOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isManager()) {
            return true;
        }

        if ($user->isSupplier()) {
            return $restockOrder->supplier_id === $user->id 
                && $user->is_approved;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, RestockOrder $restockOrder): bool
    {
        return $user->isManager() 
            && $restockOrder->manager_id === $user->id
            && $restockOrder->is_editable;
    }

    public function delete(User $user, RestockOrder $restockOrder): bool
    {
        if ($user->isAdmin()) {
            return $restockOrder->is_editable;
        }
        
        return $user->isManager() 
            && $restockOrder->manager_id === $user->id
            && $restockOrder->is_editable;
    }

    public function confirm(User $user, ?RestockOrder $restockOrder = null): bool
    {
        
        return $user->role === 'supplier' 
        && $user->is_approved == 1
        && $restockOrder->supplier_id == $user->id
        && $restockOrder->status === 'Pending';
    }

    public function deliver(User $user, RestockOrder $restockOrder): bool
    {
        return $user->role === 'supplier' 
        && $user->is_approved == 1
        && $restockOrder->supplier_id == $user->id
        && $restockOrder->status === 'Confirmed';
    }

    public function receive(User $user, RestockOrder $restockOrder): bool
    {
        return true;
    }

    public function cancel(User $user, RestockOrder $restockOrder): bool
    {
        if ($user->role === 'manager') {
            return in_array($restockOrder->status, ['Pending', 'Confirmed']);
        }
        
        if ($user->role === 'supplier' && $restockOrder->supplier_id == $user->id) {
            return in_array($restockOrder->status, ['Pending', 'Confirmed']);
        }
        
        return false;
    }

    public function viewSupplierOrders(User $user): bool
    {
        return $user->isSupplier() && $user->is_approved;
    }

    public function viewManagerOrders(User $user): bool
    {
        return $user->isManager();
    }
}