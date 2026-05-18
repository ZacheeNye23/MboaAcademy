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
        Schema::table('users', function (Blueprint $table) {
    // notifications fines
    $table->boolean('notify_mentions')->default(true);

    // UX
    $table->boolean('dark_mode')->default(false);
    $table->boolean('reduce_animations')->default(false);

    // comportement
    $table->boolean('auto_resume_course')->default(true);
    $table->boolean('hide_completed')->default(false);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
