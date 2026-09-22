<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table): void {
            $table->id();
            $table->text('qris_url')->nullable();
            $table->string('contact_person_name', 120)->nullable();
            $table->string('contact_person_whatsapp', 20)->nullable();
            $table->decimal('summit_ticket_price', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
