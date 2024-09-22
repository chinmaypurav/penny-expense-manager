<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recurring_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('person_id');
            $table->foreignId('account_id');
            $table->foreignId('category_id');
            $table->string('description');
            $table->timestamp('next_transaction_at');
            $table->string('frequency');
            $table->unsignedBigInteger('remaining_recurrences')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_incomes');
    }
};
