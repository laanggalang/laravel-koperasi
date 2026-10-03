<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('door_numbers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('door_no', 30)->unique();
            $table->string('driver_name', 150)->nullable();
            $table->string('plate_no', 15);
            $table->string('vehicle_type', 50)->nullable();
            $table->enum('status', ['active', 'available', 'inactive'])->default('active');
            $table->date('registered_date');
            $table->date('deactivated_date')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
        });

        Schema::create('door_number_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('door_number_id')->constrained('door_numbers')->cascadeOnDelete();
            $table->foreignId('from_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('to_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->enum('action', ['registered', 'transferred', 'exchanged', 'released', 'deactivated', 'activated']);
            $table->date('action_date');
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('door_number_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('door_number_histories');
        Schema::dropIfExists('door_numbers');
    }
};
