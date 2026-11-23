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
        if (! Schema::hasTable('application_document_verification_history')) {
            Schema::create('application_document_verification_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('application_document_id');
                $table->unsignedBigInteger('institution_id');
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->string('from_status', 30)->nullable();
                $table->string('to_status', 30);
                $table->text('notes')->nullable();
                $table->timestamp('reviewed_at');
                $table->timestamps();

                $table->foreign('application_document_id', 'app_doc_ver_hist_doc_fk')
                    ->references('id')->on('application_documents')->cascadeOnDelete();
                $table->foreign('institution_id', 'app_doc_ver_hist_inst_fk')
                    ->references('id')->on('institutions')->cascadeOnDelete();
                $table->foreign('reviewed_by', 'app_doc_ver_hist_user_fk')
                    ->references('id')->on('users')->nullOnDelete();
                $table->index(['application_document_id', 'reviewed_at'], 'app_doc_ver_hist_doc_review_idx');
            });

            return;
        }

        if (! Schema::hasColumn('application_document_verification_history', 'application_document_id')) {
            Schema::table('application_document_verification_history', function (Blueprint $table) {
                $table->unsignedBigInteger('application_document_id')->after('id');
            });
        }

        Schema::table('application_document_verification_history', function (Blueprint $table) {
            $table->foreign('application_document_id', 'app_doc_ver_hist_doc_fk')
                ->references('id')->on('application_documents')->cascadeOnDelete();
            $table->foreign('institution_id', 'app_doc_ver_hist_inst_fk')
                ->references('id')->on('institutions')->cascadeOnDelete();
            $table->foreign('reviewed_by', 'app_doc_ver_hist_user_fk')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['application_document_id', 'reviewed_at'], 'app_doc_ver_hist_doc_review_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_document_verification_history');
    }
};
