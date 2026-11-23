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
        Schema::create('admission_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admission_cycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->string('examination_type', 100)->nullable();
            $table->string('subject', 100)->nullable();
            $table->string('minimum_grade', 20)->nullable();
            $table->unsignedSmallInteger('minimum_subject_count')->nullable();
            $table->decimal('minimum_aggregate', 8, 2)->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            $table->index(['institution_id', 'admission_cycle_id', 'programme_id'], 'admission_req_scope_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_requirements');
    }
};
