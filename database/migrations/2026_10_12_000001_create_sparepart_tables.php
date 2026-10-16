<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Master barang sparepart
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('kode', 30)->unique();
            $table->string('nama', 150);
            $table->enum('kategori', ['oli', 'ban', 'aki', 'filter', 'spion', 'lainnya'])->default('lainnya');
            $table->string('satuan', 20)->default('pcs');
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_anggota', 15, 2);
            $table->decimal('harga_driver', 15, 2);
            $table->decimal('harga_umum', 15, 2);
            $table->decimal('stok_qty', 12, 2)->default(0);
            $table->decimal('stok_minimal', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['kategori', 'status']);
        });

        // Header struk penjualan
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('invoice_no', 50)->unique();
            $table->enum('buyer_type', ['anggota', 'driver', 'umum']);
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('driver_name', 150)->nullable();
            $table->decimal('total', 15, 2);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);
            $table->enum('status', ['paid', 'partial', 'debt'])->default('paid');
            $table->date('sale_date');
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['buyer_type', 'status', 'sale_date']);
        });

        // Detail item struk (snapshot harga saat transaksi)
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained('spareparts')->restrictOnDelete();
            $table->string('nama_snapshot', 150);
            $table->decimal('qty', 12, 2);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->index('sale_id');
        });

        // Split payment: cash / simpanan sukarela / utang
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->enum('method', ['cash', 'simpanan', 'utang']);
            $table->decimal('amount', 15, 2);
            $table->string('reference', 100)->nullable();
            $table->timestamps();

            $table->index('sale_id');
        });

        // Audit trail stok
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sparepart_id')->constrained('spareparts')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjust']);
            $table->decimal('qty', 12, 2);
            $table->decimal('stock_before', 12, 2);
            $table->decimal('stock_after', 12, 2);
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['sparepart_id', 'created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('spareparts');
    }
};
