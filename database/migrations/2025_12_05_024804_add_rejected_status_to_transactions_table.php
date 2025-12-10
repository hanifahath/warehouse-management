<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Method 1: Alter enum (MySQL)
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('Pending','Verified','Completed','Approved','Shipped','Rejected','Cancelled') DEFAULT 'Pending'");
        
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('Pending','Verified','Completed','Approved','Shipped') DEFAULT 'Pending'");
    }
};