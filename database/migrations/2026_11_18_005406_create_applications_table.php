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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_reference', 30)->unique();
            $table->foreignId('applicant_id')->constrained()->restrictOnDelete();
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('programme_id')->constrained()->restrictOnDelete();
            $table->foreignId('admission_cycle_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'documents_pending', 'awaiting_payment', 'approved', 'offered', 'accepted', 'rejected', 'withdrawn'])->default('draft')->index();
            $table->string('entry_level', 50)->nullable();
            $table->text('personal_statement')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decision_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['applicant_id', 'institution_id', 'programme_id', 'admission_cycle_id'], 'unique_applicant_application');
            $table->index(['institution_id', 'status']);
            $table->index(['admission_cycle_id', 'programme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
