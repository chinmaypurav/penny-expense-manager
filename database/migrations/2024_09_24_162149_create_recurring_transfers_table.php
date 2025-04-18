<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Account::class, 'creditor_id')->constrained();
            $table->foreignIdFor(Account::class, 'debtor_id')->constrained();
            $table->string('description')->nullable();
            $table->decimal('amount', 65);
            $table->date('next_transaction_at')->index();
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
