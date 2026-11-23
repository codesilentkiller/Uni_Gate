<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY status ENUM('draft','submitted','under_review','documents_pending','awaiting_payment','approved','offered','accepted','declined','expired','rejected','withdrawn') NOT NULL DEFAULT 'draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY status ENUM('draft','submitted','under_review','documents_pending','awaiting_payment','approved','offered','accepted','rejected','withdrawn') NOT NULL DEFAULT 'draft'");
        }
    }
};
