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
        Schema::create('admission_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->dateTime('opens_at');
            $table->dateTime('closes_at');
            $table->decimal('application_fee', 12, 2)->default(0);
            $table->enum('status', ['draft', 'open', 'closed', 'suspended'])->default('draft')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['institution_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_cycles');
    }
};
