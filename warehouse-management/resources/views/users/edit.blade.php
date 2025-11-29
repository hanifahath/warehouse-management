@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf @method('PUT')
        <x-warehouse.card>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Role</label>
                    <select name="role" class="w-full border rounded px-2 py-1" required>
                        <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Manager" {{ $user->role === 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="Staff" {{ $user->role === 'Staff' ? 'selected' : '' }}>Staff</option>
                        <option value="Supplier" {{ $user->role === 'Supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                </div>
                <div>
                    <label>Approved</label>
                    <select name="is_approved" class="w-full border rounded px-2 py-1">
                        <option value="1" {{ $user->is_approved ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$user->is_approved ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <x-warehouse.button type="primary">Update User</x-warehouse.button>
            </div>
        </x-warehouse.card>
    </form>
@endsection