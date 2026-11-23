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
        Schema::create('application_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->constrained('users')->restrictOnDelete();
            $table->enum('review_type', ['eligibility', 'departmental', 'admissions'])->default('eligibility');
            $table->enum('eligibility_status', ['pending', 'eligible', 'not_eligible', 'manual_review'])->default('pending')->index();
            $table->enum('recommendation', ['approve', 'reject', 'defer', 'manual_review'])->default('manual_review');
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();
            $table->index(['application_id', 'institution_id'], 'app_review_scope_idx');
            $table->unique(['application_id', 'review_type', 'reviewed_by'], 'app_review_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_reviews');
    }
};
