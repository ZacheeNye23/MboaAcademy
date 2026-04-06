<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {

        // ── Avis / Notes sur les cours ─────────────────────────
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // apprenant
            $table->tinyInteger('rating');         // 1 à 5
            $table->text('comment')->nullable();
            $table->unique(['course_id', 'user_id']); // un seul avis par apprenant
            $table->timestamps();
        });

        // ── Revenus par transaction ────────────────────────────
        Schema::create('revenue_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);         // montant payé
            $table->decimal('commission', 10, 2)->default(0); // commission plateforme
            $table->decimal('net_amount', 10, 2);     // ce que reçoit le formateur
            $table->string('currency')->default('XAF');
            $table->enum('status', ['pending', 'completed', 'refunded'])->default('completed');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('revenue_records');
        Schema::dropIfExists('course_reviews');
    }
};