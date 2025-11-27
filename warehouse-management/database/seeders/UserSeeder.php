<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN (Full Access) - is_approved default TRUE
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'is_approved' => true, 
        ]);

        // 2. MANAGER (Approve Transactions, Create Restock) - is_approved default TRUE
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'Manager',
            'is_approved' => true,
        ]);
        
        // 3. STAFF GUDANG (Create Transactions) - is_approved default TRUE
        User::create([
            'name' => 'Staff Gudang',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'Staff',
            'is_approved' => true,
        ]);
        
        // 4. SUPPLIER AKTIF (Sudah disetujui Admin) - Untuk pengujian Restock
        User::create([
            'name' => 'Supplier A (Approved)',
            'email' => 'supplier_a@example.com',
            'password' => Hash::make('password'),
            'role' => 'Supplier',
            'is_approved' => true, 
        ]);
        
        // 5. SUPPLIER PENDING (Perlu Approval Admin) - Untuk pengujian Manajemen Pengguna Admin
        User::create([
            'name' => 'Supplier B (Pending)',
            'email' => 'supplier_b@example.com',
            'password' => Hash::make('password'),
            'role' => 'Supplier',
            'is_approved' => false, 
        ]);
    }
}