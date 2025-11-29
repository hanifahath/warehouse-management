<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan semua Izin (Permissions) yang akan digunakan di aplikasi
        $permissions = [
            // Izin untuk Manajemen Pengguna
            'manage-users',
            'approve-user',
            
            // Izin untuk Transaksi/Penjualan
            'create-transaction',
            'view-transaction',
            'approve-transaction',

            // Izin untuk Restock (Barang Masuk)
            'create-restock',
            'view-all-restocks',
            'view-own-restocks', // Untuk Supplier
            
            // Izin untuk Data Master
            'manage-inventory',
            'manage-products',

            // Izin untuk Laporan
            'view-reports',
        ];

        // Hapus izin lama dan buat yang baru (sebagai langkah pembersihan)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Izin-Izin tersebut di tabel 'permissions'
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Ambil Role yang sudah dibuat di UserSeeder
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $staffRole = Role::where('name', 'Staff')->first();
        $supplierRole = Role::where('name', 'Supplier')->first();

        // 3. Tautkan Izin ke masing-masing Role (Mengisi role_has_permissions)

        // ADMIN: Diberi semua izin
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // MANAGER: Approval Transaksi, Buat Restock, Lihat Laporan
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'approve-transaction',
                'create-restock',
                'view-reports',
                'view-transaction',
            ]);
        }

        // STAFF GUDANG: Buat Transaksi, Kelola Inventori
        if ($staffRole) {
            $staffRole->givePermissionTo([
                'create-transaction',
                'view-transaction',
                'manage-inventory',
            ]);
        }
        
        // SUPPLIER: Hanya bisa melihat permintaan restock miliknya
        if ($supplierRole) {
            $supplierRole->givePermissionTo([
                'view-own-restocks',
            ]);
        }
        
        $this->command->info('All permissions created and assigned to roles successfully.');
    }
}