<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('creditor_id');
            $table->foreignId('debtor_id');
            $table->string('description')->nullable();
            $table->decimal('amount', 65);
            $table->date('next_transaction_at');
            $table->string('frequency')->index();
            $table->unsignedBigInteger('remaining_recurrences')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transfers');
    }
};