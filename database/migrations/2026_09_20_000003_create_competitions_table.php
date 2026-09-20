<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->decimal('registration_fee', 12, 2);
            $table->boolean('registration_open')->default(false);
            $table->timestampTz('registration_start_at')->nullable();
            $table->timestampTz('registration_end_at')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
