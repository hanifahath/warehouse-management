@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">User Management</h2>

        <a href="{{ route('admin.users.create') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + New User
        </a>
    </div>

    {{-- TABLE --}}
    <div class="bg-white border rounded shadow">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Name</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Email</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Role</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-700">Status</th>
                    <th class="px-4 py-3 text-sm font-medium text-gray-700 text-right">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        {{-- NAME --}}
                        <td class="px-4 py-3">
                            <span class="text-gray-900 font-medium">{{ $user->name }}</span>
                        </td>

                        {{-- EMAIL --}}
                        <td class="px-4 py-3 text-gray-700">
                            {{ $user->email }}
                        </td>

                        {{-- ROLE --}}
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-sm
                                @if($user->role === 'Admin') bg-indigo-100 text-indigo-800
                                @elseif($user->role === 'Warehouse Manager') bg-blue-100 text-blue-800
                                @elseif($user->role === 'Staff') bg-gray-100 text-gray-800
                                @elseif($user->role === 'Supplier') bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $user->role }}
                            </span>
                        </td>

                        {{-- STATUS (Supplier Pending Approval) --}}
                        <td class="px-4 py-3">
                            @if($user->is_approved)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                                    Approved
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">
                                    Pending
                                </span>
                            @endif
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm mr-3">
                               View
                            </a>

                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="text-gray-700 hover:text-gray-900 text-sm mr-3">
                               Edit
                            </a>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Delete this user?')"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>

</div>
@endsection
