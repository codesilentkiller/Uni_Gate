<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->text('correction_notes')->nullable()->after('personal_statement');
            $table->timestamp('correction_requested_at')->nullable()->after('correction_notes');
            $table->timestamp('applicant_confirmed_enrollment_at')->nullable()->after('orientation_completed_at');
        });

        Schema::table('application_documents', function (Blueprint $table) {
            $table->timestamp('replacement_deadline')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['correction_notes', 'correction_requested_at', 'applicant_confirmed_enrollment_at']);
        });
        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn('replacement_deadline');
        });
    }
};
