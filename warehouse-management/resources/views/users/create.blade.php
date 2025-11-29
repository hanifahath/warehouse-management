@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Create User</h1>

    @include('shared.form-errors')

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <x-warehouse.card>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Name</label>
                    <input type="text" name="name" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Password</label>
                    <input type="password" name="password" class="w-full border rounded px-2 py-1" required>
                </div>
                <div>
                    <label>Role</label>
                    <select name="role" class="w-full border rounded px-2 py-1" required>
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="Staff">Staff</option>
                        <option value="Supplier">Supplier</option>
                    </select>
                </div>
                <div>
                    <label>Approved</label>
                    <select name="is_approved" class="w-full border rounded px-2 py-1">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <x-warehouse.button type="primary">Save User</x-warehouse.button>
            </div>
        </x-warehouse.card>
    </form>
@endsection