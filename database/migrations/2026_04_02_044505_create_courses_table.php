<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // teacher
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('what_you_learn')->nullable(); // JSON ou texte
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('language')->default('fr');
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->integer('duration_minutes')->default(0); // total estimé
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('courses'); }
};