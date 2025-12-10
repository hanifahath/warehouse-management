@extends('layouts.app')

@section('content')
<div class="px-6 py-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit User: {{ $user->name }}</h1>
        <a href="{{ route('users.index') }}"
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
            ← Back
        </a>
    </div>

    @cannot('update', $user)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            You don't have permission to edit this user.
        </div>
    @else
        <div class="bg-white border border-gray-200 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Name --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" 
                               required>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password (optional) --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Password (leave blank to keep current)</label>
                        <input type="password" name="password"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Role --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Role *</label>
                        <select name="role" 
                                class="w-full border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror"
                                required>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="supplier" {{ old('role', $user->role) === 'supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                        @error('role')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hidden is_approved field --}}
                    <input type="hidden" name="is_approved" value="{{ $user->role === 'supplier' ? ($user->is_approved ? '1' : '0') : '1' }}" id="is-approved-field">
                </div>

                {{-- Supplier Approval Section --}}
                <div id="supplier-approval" class="{{ $user->role === 'supplier' ? '' : 'hidden' }} space-y-2">
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded text-sm">
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_approved_display" value="1" 
                                    {{ old('is_approved', $user->is_approved) ? 'checked' : '' }}
                                    class="supplier-approval-radio">
                                <span class="ml-2 text-sm">Approved</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="is_approved_display" value="0" 
                                    {{ !old('is_approved', $user->is_approved) ? 'checked' : '' }}
                                    class="supplier-approval-radio">
                                <span class="ml-2 text-sm">Pending Approval</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="flex items-center justify-end space-x-2 pt-4 border-t">
                    <a href="{{ route('users.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                        Update User
                    </button>
                </div>
            </form>
        </div>

        {{-- Script untuk toggle supplier approval --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const roleSelect = document.querySelector('select[name="role"]');
                const supplierApproval = document.getElementById('supplier-approval');
                const isApprovedField = document.getElementById('is-approved-field');
                const approvalRadios = document.querySelectorAll('.supplier-approval-radio');
                
                // Update hidden field when radio changes
                approvalRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        isApprovedField.value = this.value;
                    });
                });
                
                // Toggle supplier approval section
                function toggleSupplierApproval() {
                    if (roleSelect.value === 'supplier') {
                        supplierApproval.classList.remove('hidden');
                        // Set default value for supplier if not set
                        if (!isApprovedField.value) {
                            isApprovedField.value = '0';
                        }
                    } else {
                        supplierApproval.classList.add('hidden');
                        // Non-supplier always approved
                        isApprovedField.value = '1';
                    }
                }
                
                roleSelect.addEventListener('change', toggleSupplierApproval);
                toggleSupplierApproval(); // Initial call
            });
        </script>
    @endcannot
</div>
@endsection