@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <h1 class="text-2xl font-bold mb-4">User Details</h1>

    <x-warehouse.card>
        <div class="grid grid-cols-2 gap-4">
            <div><strong>Name:</strong> {{ $user->name }}</div>
            <div><strong>Email:</strong> {{ $user->email }}</div>
            <div><strong>Role:</strong> {{ $user->role }}</div>
            <div>
                <strong>Approved:</strong>
                <x-warehouse.badge status="{{ $user->is_approved ? 'ok' : 'low' }}" />
            </div>
        </div>
    </x-warehouse.card>

    <div class="mt-4 flex space-x-2">
        <a href="{{ route('users.edit', $user) }}">
            <x-warehouse.button type="primary">Edit</x-warehouse.button>
        </a>
        <form method="POST" action="{{ route('users.destroy', $user) }}">
            @csrf @method('DELETE')
            <x-warehouse.button type="danger" onclick="return confirm('Delete this user?')">
                Delete
            </x-warehouse.button>
        </form>
    </div>
@endsection