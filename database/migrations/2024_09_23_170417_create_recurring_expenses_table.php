<?php

use App\Models\Account;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Person::class)->nullable();
            $table->foreignIdFor(Account::class)->nullable()->constrained();
            $table->foreignId('category_id')->nullable();
            $table->string('description');
            $table->decimal('amount', 65, 2);
            $table->date('next_transaction_at')->index();
            $table->string('frequency')->index();
            $table->unsignedBigInteger('remaining_recurrences')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
    }
};
