@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    <x-warehouse.card>
        <div class="mb-4">
            <a href="{{ route('users.create') }}">
                <x-warehouse.button type="primary">Create New User</x-warehouse.button>
            </a>
        </div>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Name</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Role</th>
                    <th class="p-2">Approved</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-b">
                        <td class="p-2">{{ $user->name }}</td>
                        <td class="p-2">{{ $user->email }}</td>
                        <td class="p-2">{{ $user->role }}</td>
                        <td class="p-2">
                            <x-warehouse.badge status="{{ $user->is_approved ? 'ok' : 'low' }}" />
                        </td>
                        <td class="p-2 flex space-x-2">
                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('Delete this user?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @include('shared.pagination', ['paginator' => $users])
    </x-warehouse.card>
@endsection