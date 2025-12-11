<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserStatusUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->middleware(['auth']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        $filters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'status' => $request->input('status'),
        ];
        
        $users = $this->service->getFilteredUsers($filters);
        
        $counts = $this->service->getUserCounts();
        
        $unapprovedSuppliersCount = $counts['pending'];
        
        return view('users.index', compact('users', 'unapprovedSuppliersCount', 'counts'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = ['admin', 'manager', 'staff', 'supplier'];
        return view('users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request)
    {
        $this->authorize('create', User::class);

        $user = $this->service->createUser($request->validated());

        return redirect()->route('users.index')->with('success', "User {$user->name} has been added successfully.");
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = ['admin', 'manager', 'staff', 'supplier'];
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);
        
        try {
            $user = $this->service->updateUser($user, $request->validated());
            
            return redirect()->route('users.index')
                ->with('success', "User {$user->name} has been updated successfully.");
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        try {
            $this->service->deleteUser($user);
            
            return redirect()->route('users.index')
                ->with('success', "User {$user->name} has been deleted successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function updateStatus(User $user, UserStatusUpdateRequest $request)
    {
        $this->authorize('approve', $user);

        $user = $this->service->updateStatus($user, $request->validated()['is_approved']);

        $message = $request->is_approved
            ? "Supplier {$user->name} has been approved and can now login."
            : "Approval for Supplier {$user->name} has been revoked. Account deactivated.";

        return back()->with('success', $message);
    }

    public function approve(User $user)
    {
        $this->authorize('approve', $user);

        if (!$user->isSupplier()) {
            return back()->with('error', 'Only suppliers can be approved.');
        }

        $user = $this->service->updateStatus($user, true);

        return back()->with('success', 'Supplier has been approved successfully.');
    }
}