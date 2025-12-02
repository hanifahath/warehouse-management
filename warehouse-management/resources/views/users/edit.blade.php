@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-sm underline text-gray-600 hover:text-gray-800">
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="space-y-1">
            <label class="block font-medium">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="w-full border rounded px-3 py-2" required>
            @error('name')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="space-y-1">
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="w-full border rounded px-3 py-2" required>
            @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password (optional) --}}
        <div class="space-y-1">
            <label class="block font-medium">Password (leave empty to keep current)</label>
            <input type="password" name="password"
                   class="w-full border rounded px-3 py-2">
            @error('password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div class="space-y-1">
            <label class="block font-medium">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2" required>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Warehouse Manager</option>
                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff Gudang</option>
                <option value="supplier" {{ $user->role === 'supplier' ? 'selected' : '' }}>Supplier</option>
            </select>
            @error('role')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">
            Update User
        </button>
    </form>
@endsection
