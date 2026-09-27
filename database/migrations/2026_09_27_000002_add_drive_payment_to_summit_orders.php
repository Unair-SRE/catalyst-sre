<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('summit_orders', function (Blueprint $table): void {
            $table->text('payment_drive_url')->nullable()->after('sender_name');
            $table->timestampTz('payment_submitted_at')->nullable()->after('payment_drive_url');
            $table->text('review_note')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('summit_orders', function (Blueprint $table): void {
            $table->dropColumn(['payment_drive_url', 'payment_submitted_at', 'review_note']);
        });
    }
};
