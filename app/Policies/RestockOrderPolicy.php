<?php

namespace App\Policies;

use App\Models\RestockOrder;
use App\Models\User;

class RestockOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin & Manager selalu bisa
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }
        
        // Supplier hanya jika approved
        return $user->isSupplier() && $user->is_approved;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RestockOrder $restockOrder): bool
    {
        // Admin bisa lihat semua
        if ($user->isAdmin()) {
            return true;
        }

        // Manager hanya bisa lihat yang mereka buat
        if ($user->isManager()) {
            return true;
        }

        // Supplier hanya bisa lihat yang untuk mereka dan sudah approved
        if ($user->isSupplier()) {
            return $restockOrder->supplier_id === $user->id 
                && $user->is_approved;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya manager yang bisa create
        return $user->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RestockOrder $restockOrder): bool
    {
        // Manager bisa update jika masih editable
        return $user->isManager() 
            && $restockOrder->manager_id === $user->id
            && $restockOrder->is_editable;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RestockOrder $restockOrder): bool
    {
        // Admin bisa delete jika editable
        if ($user->isAdmin()) {
            return $restockOrder->is_editable;
        }

        // Manager bisa delete order mereka jika editable
        return $user->isManager() 
            && $restockOrder->manager_id === $user->id
            && $restockOrder->is_editable;
    }

    /**
     * Determine whether the user can confirm the restock order.
     */
    public function confirm(User $user, ?RestockOrder $restockOrder = null): bool
    {
        
        return $user->role === 'supplier' 
        && $user->is_approved == 1
        && $restockOrder->supplier_id == $user->id
        && $restockOrder->status === 'Pending';
    }

    /**
     * Determine whether the user can ship the restock order.
     */
    public function deliver(User $user, RestockOrder $restockOrder): bool
    {
        return $user->role === 'supplier' 
        && $user->is_approved == 1
        && $restockOrder->supplier_id == $user->id
        && $restockOrder->status === 'Confirmed';
    }

    /**
     * Determine whether the user can receive the restock order.
     */
    public function receive(User $user, RestockOrder $restockOrder): bool
    {
        return true;
        // return $user->isManager() 
        //     && in_array($restockOrder->status, ['In Transit']);
    }

    /**
     * Determine whether the user can cancel the restock order.
     */
    public function cancel(User $user, RestockOrder $restockOrder): bool
    {
        // Manager atau supplier pemilik bisa cancel jika status masih Pending/Confirmed
        if ($user->role === 'manager') {
            return in_array($restockOrder->status, ['Pending', 'Confirmed']);
        }
        
        if ($user->role === 'supplier' && $restockOrder->supplier_id == $user->id) {
            return in_array($restockOrder->status, ['Pending', 'Confirmed']);
        }
        
        return false;
    }

    /**
     * Determine whether user can view supplier-specific orders
     */
    public function viewSupplierOrders(User $user): bool
    {
        // Hanya supplier yang approved
        return $user->isSupplier() && $user->is_approved;
    }

    /**
     * Determine whether user can view manager-specific orders
     */
    public function viewManagerOrders(User $user): bool
    {
        // Hanya manager
        return $user->isManager();
    }
}