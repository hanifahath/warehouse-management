<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom status sementara
            $table->string('status')->nullable()->after('is_approved');
        });

        // Update data existing
        \Illuminate\Support\Facades\DB::table('users')
            ->where('role', 'Supplier')
            ->where('is_approved', false)
            ->update(['status' => 'pending']);

        \Illuminate\Support\Facades\DB::table('users')
            ->where('role', 'Supplier')
            ->where('is_approved', true)
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};