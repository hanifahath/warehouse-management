<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Pengguna & Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Notifikasi -->
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Tambahkan link untuk CRUD user/staff/manager lain jika diperlukan -->

                    <h3 class="font-semibold text-lg mt-6 mb-3 border-b border-gray-700 pb-2">{{ __('Daftar Pengguna') }}</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status Approval</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->role }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap" style="color: {{ $user->is_approved ? 'rgb(52, 211, 153)' : 'rgb(248, 113, 113)' }};">
                                            {{ $user->is_approved ? 'Approved' : 'PENDING' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if ($user->role === 'Supplier' && !$user->is_approved)
                                                <!-- PERBAIKAN: Menggunakan users.approve_supplier -->
                                                <form action="{{ route('users.approve_supplier', $user) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('patch') 
                                                    <button type="submit" 
                                                            class="text-blue-500 hover:text-blue-700 font-bold"
                                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui Supplier {{ $user->name }}?')">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif
                                            <!-- Tambahkan tombol Edit/Delete untuk role non-Supplier jika diperlukan -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>