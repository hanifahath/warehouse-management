<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // ===================== ADMIN USERS =====================
        $admins = [
            [
                'name' => 'Super Administrator',
                'email' => 'admin@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'Inventory System Inc.',
                'phone' => '+6281111222333',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'System Admin',
                'email' => 'sysadmin@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'Inventory System Inc.',
                'phone' => '+6281444555666',
                'address' => 'Jl. Thamrin No. 456, Jakarta',
                'email_verified_at' => now(),
            ],
        ];

        // ===================== MANAGER USERS =====================
        $managers = [
            [
                'name' => 'Warehouse Manager',
                'email' => 'manager@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_MANAGER,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'Main Warehouse',
                'phone' => '+6281777888999',
                'address' => 'Gudang Utama, Kawasan Industri, Bekasi',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'opsmanager@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_MANAGER,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'Inventory Operations',
                'phone' => '+6281999000111',
                'address' => 'Jl. Gatot Subroto No. 789, Jakarta',
                'email_verified_at' => now(),
            ],
        ];

        // ===================== STAFF USERS =====================
        $staffs = [
            [
                'name' => 'Inventory Staff 1',
                'email' => 'staff1@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_STAFF,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => null,
                'phone' => '+6281222333444',
                'address' => 'Jl. Merdeka No. 12, Jakarta',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Inventory Staff 2',
                'email' => 'staff2@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_STAFF,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => null,
                'phone' => '+6281555666777',
                'address' => 'Jl. Pemuda No. 34, Jakarta',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Stock Keeper',
                'email' => 'stock@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_STAFF,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => null,
                'phone' => '+6281888999000',
                'address' => 'Jl. Raya Bogor No. 56, Jakarta',
                'email_verified_at' => now(),
            ],
        ];

        // ===================== SUPPLIER USERS =====================
        $suppliers = [
            // Approved Suppliers
            [
                'name' => 'PT Elektronik Makmur',
                'email' => 'supplier1@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPPLIER,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'PT Elektronik Makmur Jaya',
                'phone' => '+6282111222333',
                'address' => 'Jl. Industri No. 1, Tangerang',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'CV Sinar Jaya Komputer',
                'email' => 'supplier2@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPPLIER,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'CV Sinar Jaya Komputer',
                'phone' => '+6282444555666',
                'address' => 'Mangga Dua Square Lt. 3 No. 45, Jakarta',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Toko Komputer Maju',
                'email' => 'supplier3@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPPLIER,
                'is_approved' => true,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'Toko Komputer Maju Bersama',
                'phone' => '+6282777888999',
                'address' => 'Ruko ITC Roxy Mas Blok A2 No. 12, Jakarta',
                'email_verified_at' => now(),
            ],
            
            // Pending Approval Suppliers
            [
                'name' => 'UD Teknologi Baru',
                'email' => 'supplier4@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPPLIER,
                'is_approved' => false,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'UD Teknologi Baru Indonesia',
                'phone' => '+6282999000111',
                'address' => 'Jl. Hayam Wuruk No. 78, Jakarta',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Berkah Komputer',
                'email' => 'supplier5@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPPLIER,
                'is_approved' => false,
                'status' => User::STATUS_ACTIVE,
                'company_name' => 'Berkah Komputer Abadi',
                'phone' => '+6283111222333',
                'address' => 'Jl. Pasar Baru No. 90, Bandung',
                'email_verified_at' => now(),
            ],
            
            // Inactive Supplier
            [
                'name' => 'Supplier Nonaktif',
                'email' => 'inactive@inventory.test',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_SUPPLIER,
                'is_approved' => true,
                'status' => User::STATUS_INACTIVE,
                'company_name' => 'PT Supplier Nonaktif',
                'phone' => '+6283444555666',
                'address' => 'Jl. Veteran No. 67, Surabaya',
                'email_verified_at' => now()->subMonths(6),
            ],
        ];

        // Combine all users
        $allUsers = array_merge($admins, $managers, $staffs, $suppliers);

        // Insert users
        foreach ($allUsers as $userData) {
            User::create($userData);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Total: ' . count($allUsers) . ' users created.');
        $this->command->info('Admins: 2, Managers: 2, Staff: 3, Suppliers: 6 (3 approved, 2 pending, 1 inactive)');
        
        // Login credentials info
        $this->command->line('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('All users password: password123');
        $this->command->info('');
        $this->command->info('Admin: admin@inventory.test');
        $this->command->info('Manager: manager@inventory.test');
        $this->command->info('Staff: staff1@inventory.test');
        $this->command->info('Approved Supplier: supplier1@inventory.test');
        $this->command->info('Pending Supplier: supplier4@inventory.test');
    }
}