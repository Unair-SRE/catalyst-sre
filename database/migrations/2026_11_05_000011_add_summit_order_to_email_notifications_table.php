<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_notifications', function (Blueprint $table): void {
            $table->foreignId('summit_order_id')->nullable()->after('user_id')
                ->constrained()->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('email_notifications', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('summit_order_id');
        });
    }
};
