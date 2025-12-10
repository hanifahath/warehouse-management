@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New User</h1>
        <a href="{{ route('users.index') }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
            ← Back
        </a>
    </div>

    @cannot('create', App\Models\User::class)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            You don't have permission to create users.
        </div>
    @else
        <div class="bg-white border border-gray-200 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Name --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" 
                               required>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Password *</label>
                        <input type="password" name="password"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror"
                               required>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    {{-- Role --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Role *</label>
                        <select name="role" 
                                class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror"
                                required>
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="supplier" {{ old('role') === 'supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                        @error('role')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- HIDDEN FIELD untuk is_approved --}}
                    <input type="hidden" name="is_approved" value="1">
                </div>

                {{-- Supplier Note --}}
                <div id="supplier-note" class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                    <strong>Note:</strong> New suppliers will automatically have <em>"Pending Approval"</em> status.
                    You can approve them later from the users list.
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center justify-end space-x-2 pt-4 border-t">
                    <a href="{{ route('users.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                        Create User
                    </button>
                </div>
            </form>
        </div>

        {{-- Script untuk toggle supplier note --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const roleSelect = document.querySelector('select[name="role"]');
                const supplierNote = document.getElementById('supplier-note');
                const isApprovedField = document.querySelector('input[name="is_approved"]');
                
                function toggleSupplierNote() {
                    if (roleSelect.value === 'supplier') {
                        supplierNote.classList.remove('hidden');
                        isApprovedField.value = '0'; // Supplier default: pending
                    } else {
                        supplierNote.classList.add('hidden');
                        isApprovedField.value = '1'; // Non-supplier default: approved
                    }
                }
                
                roleSelect.addEventListener('change', toggleSupplierNote);
                toggleSupplierNote(); // Initial call
            });
        </script>
    @endcannot
</div>
@endsection