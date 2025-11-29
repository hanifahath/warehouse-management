<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('restock_items', function (Blueprint $table) {
                // Tambahkan kolom unit_price setelah kolom quantity
                $table->decimal('unit_price', 15, 2)->after('quantity'); 
            });
        }

        public function down(): void
        {
            Schema::table('restock_items', function (Blueprint $table) {
                // Hapus kolom jika rollback
                $table->dropColumn('unit_price');
            });
        }
    };