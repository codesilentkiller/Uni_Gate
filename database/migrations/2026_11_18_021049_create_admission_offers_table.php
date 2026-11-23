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
        Schema::create('admission_offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_reference', 30)->unique();
            $table->foreignId('admission_decision_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('application_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entry_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->enum('offer_status', ['draft', 'issued', 'accepted', 'declined', 'expired', 'withdrawn'])->default('draft')->index();
            $table->text('offer_message')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['institution_id', 'offer_status'], 'admission_offer_scope_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_offers');
    }
};
