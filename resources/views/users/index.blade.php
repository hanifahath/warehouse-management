@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
        @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}"
               class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">
                + Add User
            </a>
        @endcan
    </div>

    {{-- NOTIFICATIONS --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @cannot('viewAny', App\Models\User::class)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            You don't have permission to view users.
        </div>
    @else

        {{-- FILTERS --}}
        <form method="GET" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- SEARCH --}}
                <div>
                    <input type="text" name="search" placeholder="Search by name or email..."
                           value="{{ request('search') }}"
                           class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- ROLE FILTER --}}
                <div>
                    <select name="role"
                            class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="supplier" {{ request('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>

                {{-- STATUS FILTER --}}
                <div>
                    <select name="status"
                            class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>

                {{-- ACTIONS --}}
                <div class="flex gap-2">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                        Apply Filter
                    </button>
                    <a href="{{ route('users.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- ACTIVE FILTERS --}}
        @if(request()->anyFilled(['search', 'role', 'status']))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
            <strong>Active Filters:</strong>
            @if(request('search')) 
                <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Search: "{{ request('search') }}"</span> 
            @endif
            @if(request('role')) 
                <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Role: {{ ucfirst(request('role')) }}</span> 
            @endif
            @if(request('status')) 
                <span class="ml-2 px-2 py-1 bg-blue-100 rounded">Status: {{ ucfirst(request('status')) }}</span> 
            @endif
        </div>
        @endif

        {{-- SUPPLIER PENDING COUNT --}}
        @if($unapprovedSuppliersCount > 0)
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                <strong>⚠️ Attention:</strong> There are {{ $unapprovedSuppliersCount }} supplier(s) pending approval.
            </div>
        @endif

        {{-- TABLE --}}
        <div class="bg-white border border-gray-200 shadow rounded-lg overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 font-medium text-gray-600">Name</th>
                        <th class="py-3 px-4 font-medium text-gray-600">Email</th>
                        <th class="py-3 px-4 font-medium text-gray-600">Role</th>
                        <th class="py-3 px-4 font-medium text-gray-600">Status</th>
                        <th class="py-3 px-4 font-medium text-gray-600 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        @php
                            $roleColors = [
                                'admin' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                'manager' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                'staff' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                'supplier' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                            ];
                            $roleColor = $roleColors[$user->role] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                        @endphp
                        
                        <tr class="hover:bg-gray-50">
                            {{-- NAME --}}
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <div class="text-gray-500 text-xs mt-1">(You)</div>
                                @endif
                            </td>

                            {{-- EMAIL --}}
                            <td class="py-3 px-4 text-gray-700">
                                {{ $user->email }}
                            </td>

                            {{-- ROLE --}}
                            <td class="py-3 px-4">
                                <span class="{{ $roleColor['bg'] }} {{ $roleColor['text'] }} px-2 py-1 rounded text-xs">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="py-3 px-4">
                                @if($user->role === 'supplier')
                                    @if($user->is_approved)
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                            Approved
                                        </span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                            Pending Approval
                                        </span>
                                    @endif
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">
                                        Active
                                    </span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td class="py-3 px-4 text-right">
                                <div class="flex justify-end gap-2">
                                    @can('view', $user)
                                        <a href="{{ route('users.show', $user->id) }}"
                                           class="px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded text-sm"
                                           title="View">
                                            View
                                        </a>
                                    @endcan

                                    @can('update', $user)
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="px-3 py-1 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded text-sm"
                                           title="Edit">
                                            Edit
                                        </a>
                                    @endcan

                                    @can('approve', $user)
                                        @if($user->role === 'supplier' && !$user->is_approved)
                                            <form action="{{ route('users.approve', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Approve this supplier?')"
                                                        class="px-3 py-1 bg-green-50 text-green-600 hover:bg-green-100 rounded text-sm"
                                                        title="Approve">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('delete', $user)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Delete user {{ $user->name }}?')"
                                                    class="px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded text-sm"
                                                    title="Delete">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <div class="mb-2">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                No users found.
                                @if(request()->anyFilled(['search', 'role', 'status']))
                                    <div class="mt-2">
                                        <a href="{{ route('users.index') }}" 
                                           class="text-indigo-600 hover:underline text-sm">
                                            Clear filters
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($users->hasPages())
        <div class="mt-6">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    @endcannot
</div>
@endsection