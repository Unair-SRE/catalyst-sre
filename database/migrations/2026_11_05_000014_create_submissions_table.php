<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('competition_round_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('public_id')->unique();
            $table->text('file_url');
            $table->unsignedBigInteger('bytes');
            $table->string('format', 10);
            $table->string('etag')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestampTz('submitted_at')->nullable();
            $table->timestampsTz();

            $table->unique(['registration_id', 'competition_round_id']);
            $table->index(['competition_round_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
