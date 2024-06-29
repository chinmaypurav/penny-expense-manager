<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Person::class)->nullable();
            $table->foreignIdFor(Account::class);
            $table->foreignIdFor(Category::class)->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->timestamp('transacted_at');
            $table->decimal('amount', 65);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
