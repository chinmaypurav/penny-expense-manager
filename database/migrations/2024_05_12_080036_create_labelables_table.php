<?php

use App\Models\Label;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labelables', function (Blueprint $table) {
            $table->foreignIdFor(Label::class);
            $table->morphs('labelable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labelables');
    }
};
