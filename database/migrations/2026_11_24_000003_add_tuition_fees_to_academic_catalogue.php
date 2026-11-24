<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faculties', function (Blueprint $table) {
            $table->decimal('tuition_fee', 12, 2)->nullable()->after('description');
        });

        Schema::table('programmes', function (Blueprint $table) {
            $table->decimal('tuition_fee', 12, 2)->nullable()->after('entry_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('faculties', fn (Blueprint $table) => $table->dropColumn('tuition_fee'));
        Schema::table('programmes', fn (Blueprint $table) => $table->dropColumn('tuition_fee'));
    }
};
