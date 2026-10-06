<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Nomor pintu kini didaftarkan dulu ke pool KBP (available) tanpa kendaraan;
        // plat & jenis kendaraan menyusul saat diambil/diisi per nomor pintu.
        Schema::table('door_numbers', function (Blueprint $table) {
            $table->string('plate_no', 15)->nullable()->change();
        });
    }

    public function down(): void {
        Schema::table('door_numbers', function (Blueprint $table) {
            $table->string('plate_no', 15)->nullable(false)->change();
        });
    }
};
