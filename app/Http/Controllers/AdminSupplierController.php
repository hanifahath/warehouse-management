<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminSupplierController extends Controller
{
    public function pending()
    {
        // Gunakan OR condition untuk handle kedua kemungkinan
        $suppliers = User::where('role', 'Supplier')
            ->where(function($query) {
                $query->where('is_approved', false)
                      ->orWhere('status', 'pending');
            })
            ->get();

        return view('admin.suppliers.pending', compact('suppliers'));
    }

    public function approve(User $user)
    {
        $user->update([
            'is_approved' => true,
            'status' => 'approved', // Update kedua kolom
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        return back()->with('success', 'Supplier disetujui.');
    }

    public function reject(User $user)
    {
        $user->update([
            'is_approved' => false,
            'status' => 'rejected', // Update kedua kolom
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Supplier ditolak.');
    }
}