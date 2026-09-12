<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('loan_no',50)->unique();
            $table->decimal('principal',15,2);
            $table->decimal('interest_rate',8,2)->default(0);
            $table->unsignedInteger('tenor');
            $table->decimal('monthly_payment',15,2);
            $table->decimal('total_payment',15,2);
            $table->decimal('remaining_balance',15,2);
            $table->date('start_date');
            $table->enum('status',['active','paid','cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('loans'); }
};
