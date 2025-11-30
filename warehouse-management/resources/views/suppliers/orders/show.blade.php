@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Users Management</h1>

    {{-- Asumsi x-warehouse.card --}}
    <x-warehouse.card>
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('admin.users.create') }}">
                <x-warehouse.button type="primary">
                    {{-- Icon Create --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Create New User
                </x-warehouse.button>
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
                        <th class="p-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="p-3 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $user->role }}</td>
                            <td class="p-3 text-sm">
                                <x-warehouse.badge status="{{ $user->is_approved ? 'ok' : 'low' }}" />
                            </td>
                            
                            {{-- KOLOM AKSI --}}
                            <td class="p-3 flex items-center space-x-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800 transition duration-150 ease-in-out font-medium">Edit</a>
                                <span class="text-gray-300">|</span>
                                
                                {{-- FORM HAPUS DENGAN ID UNIK --}}
                                <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block">
                                    @csrf @method('DELETE')
                                    
                                    {{-- Tombol bertipe "button" untuk mencegah submission otomatis. --}}
                                    <button type="button" 
                                            class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out font-medium"
                                            onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('shared.pagination', ['paginator' => $users])
    </x-warehouse.card>
@endsection

@section('scripts')
{{-- PASTIKAN SCRIPT INI ADA DAN DIMUAT SETELAH JQUERY/BOOTSTRAP JIKA MENGGUNAKANNYA --}}
<script>
    // Fungsi JavaScript untuk menangani konfirmasi delete yang lebih kontekstual
    function confirmDelete(userId, userName) {
        // Menggunakan konfirmasi bawaan browser
        const isConfirmed = confirm(`Apakah Anda yakin ingin menghapus pengguna: ${userName}? \n\nTindakan ini tidak dapat dibatalkan.`);

        if (isConfirmed) {
            // Mengirimkan form yang sesuai
            const form = document.getElementById('delete-form-' + userId);
            if (form) {
                form.submit();
            } else {
                console.error('Formulir tidak ditemukan untuk ID:', userId);
            }
        } else {
            console.log(`Penghapusan untuk pengguna ${userName} dibatalkan.`);
        }
    }
</script>
@endsection