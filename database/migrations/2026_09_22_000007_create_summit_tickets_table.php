<?php

use App\Enums\SummitTicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summit_tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('summit_order_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('holder_name', 120);
            $table->string('ticket_code', 10)->unique();
            $table->text('pdf_url')->nullable();
            $table->string('pdf_file_id')->nullable();
            $table->string('status', 20)->default(SummitTicketStatus::WaitingPayment->value);
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestampTz('checked_in_at')->nullable();
            $table->timestampsTz();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('summit_tickets');
    }
};
