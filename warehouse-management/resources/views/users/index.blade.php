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
                    
                    <!-- Notifikasi Error Validasi (PENTING untuk debugging) -->
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-400" role="alert">
                            <strong>Kesalahan Validasi:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Statistik Filter (Opsional) -->
                    <div class="mb-4">
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Semua Pengguna
                        </a>
                        <a href="{{ route('users.index', ['filter' => 'unapproved_suppliers']) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Supplier Pending ({{ $unapprovedSuppliersCount }})
                        </a>
                    </div>


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
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->role === 'Supplier')
                                                <span class="font-bold {{ $user->is_approved ? 'text-green-500' : 'text-red-500' }}">
                                                    {{ $user->is_approved ? 'Approved' : 'PENDING' }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if ($user->role === 'Supplier')
                                                <form action="{{ route('users.update_status', $user) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('patch') 

                                                    @if (!$user->is_approved)
                                                        <!-- Kunci Perbaikan 1: Menambahkan input is_approved=1 (Approve) -->
                                                        <input type="hidden" name="is_approved" value="1"> 
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                                onclick="return confirm('Apakah Anda yakin ingin menyetujui Supplier {{ $user->name }}?')">
                                                            Approve
                                                        </button>
                                                    @else
                                                        <!-- Kunci Perbaikan 2: Menambahkan input is_approved=0 (Batalkan/Reject) -->
                                                        <input type="hidden" name="is_approved" value="0">
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                                onclick="return confirm('Apakah Anda yakin ingin membatalkan persetujuan Supplier {{ $user->name }}?')">
                                                            Batalkan
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                            
                                            <!-- Tombol Hapus Umum (Jangan tampilkan untuk Admin yang sedang login) -->
                                            @if (auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900" 
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
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