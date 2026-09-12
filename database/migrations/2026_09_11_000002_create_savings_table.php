<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('type',['pokok','wajib','sukarela']);
            $table->decimal('amount',15,2);
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->index(['member_id','type']);
        });
    }
    public function down(): void { Schema::dropIfExists('savings'); }
};
