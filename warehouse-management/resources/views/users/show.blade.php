@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">User Details</h2>

        <a href="{{ route('admin.users.index') }}"
           class="text-indigo-600 hover:text-indigo-800 text-sm">
            ← Back to Users
        </a>
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white border rounded shadow p-6">

        {{-- NAME --}}
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Name</h3>
            <p class="text-gray-700">{{ $user->name }}</p>
        </div>

        {{-- EMAIL --}}
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Email</h3>
            <p class="text-gray-700">{{ $user->email }}</p>
        </div>

        {{-- ROLE --}}
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Role</h3>
            <span class="px-3 py-1 rounded text-sm
                @if($user->role === 'Admin') bg-indigo-100 text-indigo-800
                @elseif($user->role === 'Warehouse Manager') bg-blue-100 text-blue-800
                @elseif($user->role === 'Staff') bg-gray-100 text-gray-800
                @elseif($user->role === 'Supplier') bg-yellow-100 text-yellow-800
                @endif">
                {{ $user->role }}
            </span>
        </div>

        {{-- STATUS --}}
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Status</h3>
            @if($user->is_approved)
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm">
                    Approved
                </span>
            @else
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded text-sm">
                    Pending Approval
                </span>
            @endif
        </div>

        {{-- ACTIONS --}}
        <div class="border-t pt-6 mt-6 flex items-center justify-between">

            <div class="flex space-x-3">
                {{-- EDIT --}}
                <a href="{{ route('admin.users.edit', $user->id) }}"
                   class="bg-gray-100 text-gray-800 px-4 py-2 rounded hover:bg-gray-200">
                    Edit
                </a>

                {{-- DELETE --}}
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                      onsubmit="return confirm('Delete this user?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>

            {{-- APPROVE SUPPLIER (Jika belum approved) --}}
            @if($user->role === 'Supplier' && !$user->is_approved)
                <form action="{{ route('admin.users.update_status', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_approved" value="1"> 
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Approve Supplier
                    </button>
                </form>
            @endif

        </div>

    </div>

</div>
@endsection
