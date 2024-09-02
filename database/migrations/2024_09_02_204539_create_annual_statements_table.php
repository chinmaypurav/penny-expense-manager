<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('financial_year_id');
            $table->decimal('salary', 65, 2);
            $table->decimal('dividend', 65, 2);
            $table->decimal('interest', 65, 2);
            $table->decimal('stcg', 65, 2);
            $table->decimal('ltcg', 65, 2);
            $table->decimal('other_income', 65, 2);
            $table->decimal('tax_paid', 65, 2);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_statements');
    }
};
