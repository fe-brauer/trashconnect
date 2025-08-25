<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('participants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('season_id')->constrained()->cascadeOnDelete();
            $t->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $t->string('role')->nullable();       // Kandidat:in, Host, etc.
            $t->string('placement')->nullable();  // Platzierung
            $t->decimal('prize_won', 10, 2)->nullable();
            $t->timestamps();

            $t->unique(['season_id', 'candidate_id']);
            $t->index(['candidate_id', 'season_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('participants');
    }
};
