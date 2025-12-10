<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah kolom yang kurang
            $table->text('rejection_reason')->nullable()->after('notes');
            $table->timestamp('completed_at')->nullable()->after('approved_at');
            $table->timestamp('shipped_at')->nullable()->after('completed_at');
            $table->decimal('total_amount', 12, 2)->default(0)->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'completed_at', 'shipped_at', 'total_amount']);
        });
    }
};