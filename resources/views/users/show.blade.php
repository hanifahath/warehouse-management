@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Detail Pengguna</h2>
        <a href="{{ route('users.index') }}"
           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
            ← Kembali ke Daftar
        </a>
    </div>

    @cannot('view', $user)
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            Anda tidak memiliki izin untuk melihat detail pengguna ini.
        </div>
    @else
        {{-- USER INFO CARD --}}
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-medium text-gray-900">Informasi Pengguna</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                        <p class="text-lg font-medium text-gray-900">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="text-lg text-gray-900">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Role</p>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            @if($user->role === 'admin') bg-red-100 text-red-800
                            @elseif($user->role === 'manager') bg-yellow-100 text-yellow-800
                            @elseif($user->role === 'staff') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        @if($user->role === 'supplier')
                            @if($user->is_approved)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Disetujui
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Persetujuan
                                </span>
                            @endif
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- CREATED AT --}}
                <div class="mt-6 pt-6 border-t">
                    <p class="text-sm text-gray-500">Dibuat pada</p>
                    <p class="text-gray-900">{{ $user->created_at->translatedFormat('d F Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    @can('update', $user)
                    <a href="{{ route('users.edit', $user->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        Edit Pengguna
                    </a>
                    @endcan

                    @can('delete', $user)
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                          onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                            Hapus Pengguna
                        </button>
                    </form>
                    @endcan
                </div>

                {{-- APPROVE SUPPLIER BUTTON --}}
                @can('approve', $user)
                    @if($user->role === 'supplier' && !$user->is_approved)
                        <form action="{{ route('users.approve', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Setujui supplier {{ $user->name }}?')"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                Setujui Supplier
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    @endcannot
</div>
@endsection