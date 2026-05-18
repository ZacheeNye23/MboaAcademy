<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('admin_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('action');          // ex: 'approve_course', 'ban_user'
    $table->text('description')->nullable();
    $table->string('ip_address')->nullable();
    $table->json('meta')->nullable();  // données supplémentaires
    $table->timestamps();
});
schema::create('login_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('email');
    $table->string('ip_address')->nullable();
    $table->string('user_agent')->nullable();
    $table->enum('status', ['success', 'failed']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('login_attempts'); 
    }
};
