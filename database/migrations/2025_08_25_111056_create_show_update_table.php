<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('show_update', function (Blueprint $table) {
            $table->foreignId('show_id')->constrained()->cascadeOnDelete();
            $table->foreignId('update_id')->constrained()->cascadeOnDelete();
            $table->primary(['show_id','update_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('show_update');
    }
};
