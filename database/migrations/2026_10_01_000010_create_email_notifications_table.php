<?php

use App\Enums\EmailStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('recipient_email');
            $table->string('type', 40);
            $table->string('status', 20)->default(EmailStatus::Pending->value);
            $table->timestampTz('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestampsTz();

            $table->index(['status']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
