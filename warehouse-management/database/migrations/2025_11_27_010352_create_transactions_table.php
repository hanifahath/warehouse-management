<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_number')->unique();
            $table->enum('type', ['Incoming', 'Outgoing']);

            // Relasi Pembuat (Staff Gudang)
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Relasi Approval (Manager) - PENTING!
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); 

            // Waktu Approval (BARU DITAMBAHKAN)
            $table->timestamp('approved_at')->nullable();

            // Relasi Supplier (Jika type = Incoming)
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('users') // Mengarah ke tabel users karena Supplier adalah role
                ->nullOnDelete();

            // Untuk transaksi outgoing
            $table->string('customer_name')->nullable();

            // Status transaksi
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Completed'])
                ->default('Pending');

            $table->date('date'); // Tanggal transaksi
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};