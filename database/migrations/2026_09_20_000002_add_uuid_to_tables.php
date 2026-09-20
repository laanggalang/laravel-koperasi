<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tambah kolom uuid (nullable dulu untuk backfill data lama)
        foreach (['members', 'savings', 'loans', 'installments'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->uuid('uuid')->nullable()->after('id');
            });
        }

        // Backfill data lama
        foreach (['members', 'savings', 'loans', 'installments'] as $table) {
            DB::table($table)->whereNull('uuid')->orderBy('id')->chunkById(500, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
                }
            });
        }

        // Unique index
        foreach (['members', 'savings', 'loans', 'installments'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->unique('uuid');
            });
        }
    }

    public function down(): void {
        foreach (['members', 'savings', 'loans', 'installments'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropUnique(['uuid']);
                $t->dropColumn('uuid');
            });
        }
    }
};
