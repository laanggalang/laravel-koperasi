<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_no',30)->unique();
            $table->string('name',150);
            $table->string('nik',30)->nullable();
            $table->string('phone',30)->nullable();
            $table->string('email',150)->nullable();
            $table->text('address')->nullable();
            $table->date('join_date');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('members'); }
};
