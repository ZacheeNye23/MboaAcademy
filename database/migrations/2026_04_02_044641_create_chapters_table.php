<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('video_path')->nullable();
            $table->string('video_url')->nullable();  // URL externe (YouTube, Vimeo)
            $table->integer('duration')->nullable();  // secondes
            $table->integer('order')->default(0);
            $table->boolean('is_free')->default(false); // aperçu gratuit
            $table->enum('type', ['video', 'text', 'mixed'])->default('video');
            $table->timestamps();
        });

        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->enum('type', ['pdf', 'doc', 'link', 'zip', 'other'])->default('pdf');
            $table->bigInteger('file_size')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('resources');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('chapters');
    }
};