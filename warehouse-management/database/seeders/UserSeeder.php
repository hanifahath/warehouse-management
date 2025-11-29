<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // PENTING: Import model Role Spatie

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PASTIKAN ROLE DIBUAT DAHULU di tabel 'roles'
        // Jika sudah ada, tidak akan dibuat ulang (firstOrCreate)
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleManager = Role::firstOrCreate(['name' => 'Manager']);
        $roleStaff = Role::firstOrCreate(['name' => 'Staff']);
        $roleSupplier = Role::firstOrCreate(['name' => 'Supplier']);


        // --- 2. USER ADMIN (Full Access) ---
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin', // (Opsional) Tetap isi kolom kustom jika Anda membutuhkannya
            'is_approved' => true, 
        ]);
        // BARIS PENTING: Menautkan Role ke User (mengisi tabel model_has_roles)
        $admin->assignRole($roleAdmin);


        // --- 3. MANAGER ---
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'Manager',
            'is_approved' => true,
        ]);
        $manager->assignRole($roleManager);
        
        // --- 4. STAFF GUDANG ---
        $staff = User::create([
            'name' => 'Staff Gudang',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'Staff',
            'is_approved' => true,
        ]);
        $staff->assignRole($roleStaff);
        
        // --- 5. SUPPLIER AKTIF ---
        $supplierA = User::create([
            'name' => 'Supplier A (Approved)',
            'email' => 'supplier_a@example.com',
            'password' => Hash::make('password'),
            'role' => 'Supplier',
            'is_approved' => true, 
        ]);
        $supplierA->assignRole($roleSupplier);
        
        // --- 6. SUPPLIER PENDING ---
        $supplierB = User::create([
            'name' => 'Supplier B (Pending)',
            'email' => 'supplier_b@example.com',
            'password' => Hash::make('password'),
            'role' => 'Supplier',
            'is_approved' => false, 
        ]);
        $supplierB->assignRole($roleSupplier);
        
        $this->command->info('Roles and users successfully seeded and linked to Spatie.');
    }
}