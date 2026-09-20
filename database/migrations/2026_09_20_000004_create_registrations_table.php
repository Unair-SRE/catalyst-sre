<?php

use App\Enums\RegistrationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('competition_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status', 20)->default(RegistrationStatus::Pending->value);
            $table->timestampsTz();

            $table->unique(['team_id', 'competition_id']);
            $table->index(['competition_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
