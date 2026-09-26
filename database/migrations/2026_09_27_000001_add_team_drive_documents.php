<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->text('documents_drive_url')->nullable();
        });
        Schema::table('team_members', function (Blueprint $table): void {
            $table->text('ktm_url')->nullable()->change();
            $table->string('ktm_file_id')->nullable()->change();
        });
        Schema::table('payments', function (Blueprint $table): void {
            $table->timestampTz('documents_submitted_at')->nullable();
            $table->text('review_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', fn (Blueprint $table) => $table->dropColumn(['documents_submitted_at', 'review_note']));
        Schema::table('teams', fn (Blueprint $table) => $table->dropColumn('documents_drive_url'));
        // Keep legacy KTM columns nullable so rollback does not destroy Drive-only members.
    }
};
