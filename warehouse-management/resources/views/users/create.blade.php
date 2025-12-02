@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Create User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-sm underline text-gray-600 hover:text-gray-800">
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        {{-- Name --}}
        <div class="space-y-1">
            <label class="block font-medium">Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="w-full border rounded px-3 py-2" required>
            @error('name')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="space-y-1">
            <label class="block font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border rounded px-3 py-2" required>
            @error('email')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-1">
            <label class="block font-medium">Password</label>
            <input type="password" name="password"
                   class="w-full border rounded px-3 py-2" required>
            @error('password')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tambahkan field konfirmasi password ini --}}
        <div class="space-y-1 mt-4">
            <label class="block font-medium">Konfirmasi Password</label>
            <input type="password" name="password_confirmation"
                class="w-full border rounded px-3 py-2" required>
            
            {{-- Error message --}}
            @error('password_confirmation')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div class="space-y-1">
            <label class="block font-medium">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Choose Role --</option>
                <option value="Admin" {{ old('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Manager" {{ old('role') === 'Manager' ? 'selected' : '' }}>Warehouse Manager</option>
                <option value="Staff" {{ old('role') === 'Staff' ? 'selected' : '' }}>Staff Gudang</option>
                <option value="Supplier" {{ old('role') === 'Supplier' ? 'selected' : '' }}>Supplier</option>
            </select>
            @error('role')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">
            Create User
        </button>
    </form>
@endsection
