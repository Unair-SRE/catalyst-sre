<?php

use App\Enums\QualificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualification_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('competition_round_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status', 20)->default(QualificationStatus::NotQualified->value);
            $table->foreignId('decided_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestampTz('decided_at')->nullable();
            $table->timestampsTz();

            $table->unique(['registration_id', 'competition_round_id']);
            $table->index(['competition_round_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualification_results');
    }
};
