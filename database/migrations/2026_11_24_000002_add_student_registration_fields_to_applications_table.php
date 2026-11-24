<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('course_level', 50)->nullable()->after('entry_level');
            $table->string('entry_status', 50)->nullable()->after('course_level');
            $table->timestamp('orientation_completed_at')->nullable()->after('enrolled_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['course_level', 'entry_status', 'orientation_completed_at']);
        });
    }
};
