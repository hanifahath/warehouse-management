<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Jalankan migrasi.
         */
        public function up(): void
        {
            Schema::table('products', function (Blueprint $table) {
                // Menambahkan foreign key 'supplier_id' yang menunjuk ke kolom 'id' di tabel 'users'.
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->after('category_id') 
                    ->constrained('users') // Penting: pastikan menunjuk ke tabel 'users'
                    ->nullOnDelete(); 
            });
        }

        /**
         * Balikkan migrasi.
         */
        public function down(): void
        {
            Schema::table('products', function (Blueprint $table) {
                // Hapus foreign key dan kolomnya
                $table->dropConstrainedForeignId('supplier_id');
            });
        }
    };