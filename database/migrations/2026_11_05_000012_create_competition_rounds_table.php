<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_rounds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name', 120);
            $table->unsignedInteger('round_order');
            $table->timestampTz('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestampsTz();

            $table->unique(['competition_id', 'round_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_rounds');
    }
};
