<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'is_approved' => true,
            ]
        );

        // Manager
        User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'role' => 'Manager',
                'is_approved' => true,
            ]
        );

        // Staff
        User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'role' => 'Staff',
                'is_approved' => true,
            ]
        );

        // Supplier Approved
        User::firstOrCreate(
            ['email' => 'supplier_a@example.com'],
            [
                'name' => 'Supplier A',
                'password' => Hash::make('password'),
                'role' => 'Supplier',
                'is_approved' => true,
            ]
        );

        // Supplier Pending
        User::firstOrCreate(
            ['email' => 'supplier_b@example.com'],
            [
                'name' => 'Supplier B',
                'password' => Hash::make('password'),
                'role' => 'Supplier',
                'is_approved' => false,
            ]
        );
    }
}
