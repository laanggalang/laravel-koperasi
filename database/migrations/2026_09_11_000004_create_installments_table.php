<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('installment_no');
            $table->decimal('amount',15,2);
            $table->date('paid_date');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['loan_id','installment_no']);
        });
    }
    public function down(): void { Schema::dropIfExists('installments'); }
};
