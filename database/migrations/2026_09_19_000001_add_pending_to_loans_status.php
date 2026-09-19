<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan status 'pending' ke kolom status tabel loans
     * agar alur pengajuan -> persetujuan pinjaman berjalan.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'paid', 'cancelled'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->enum('status', ['active', 'paid', 'cancelled'])->default('active')->change();
        });
    }
};
