<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table): void {
            $table->timestampTz('starts_at')->nullable()->after('round_order');
            $table->timestampTz('ends_at')->nullable()->after('starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('competition_rounds', function (Blueprint $table): void {
            $table->dropColumn(['starts_at', 'ends_at']);
        });
    }
};
