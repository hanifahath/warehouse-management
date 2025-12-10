<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Untuk MySQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff', 'supplier') NOT NULL");
        
        // Update existing data (case-sensitive)
        DB::statement("UPDATE users SET role = 'admin' WHERE role = 'Admin'");
        DB::statement("UPDATE users SET role = 'manager' WHERE role = 'Manager'");
        DB::statement("UPDATE users SET role = 'staff' WHERE role = 'Staff'");
        DB::statement("UPDATE users SET role = 'supplier' WHERE role = 'Supplier'");
    }

    public function down(): void
    {
        // Rollback ke uppercase
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Admin', 'Manager', 'Staff', 'Supplier') NOT NULL");
    }
};