<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('sender_name', 120)->nullable();
            $table->text('payment_proof_url')->nullable();
            $table->string('payment_proof_file_id')->nullable();
            $table->string('status', 24)->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestampTz('verified_at')->nullable();
            $table->timestampsTz();

            $table->index('status');
        });

        DB::table('registrations')
            ->orderBy('id')
            ->chunkById(500, function ($registrations): void {
                $now = now();
                DB::table('payments')->insert(
                    $registrations->map(fn ($registration): array => [
                        'registration_id' => $registration->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all(),
                );
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
