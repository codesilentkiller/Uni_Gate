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
        if (! Schema::hasTable('examination_results')) {
            Schema::create('examination_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
                $table->string('examination_type', 100);
                $table->year('examination_year');
                $table->string('index_number', 100)->nullable();
                $table->string('subject', 100);
                $table->string('grade', 20);
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->index();
                $table->timestamps();
                $table->unique(['applicant_id', 'examination_type', 'examination_year', 'subject', 'index_number'], 'exam_result_unique');
                $table->index(['applicant_id', 'examination_type', 'examination_year'], 'exam_result_applicant_idx');
            });

            return;
        }

        Schema::table('examination_results', function (Blueprint $table) {
            $table->index(['applicant_id', 'examination_type', 'examination_year'], 'exam_result_applicant_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_results');
    }
};
