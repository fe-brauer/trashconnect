<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seo_data', function (Blueprint $t) {
            $t->id();
            $t->morphs('model'); // model_type, model_id
            $t->string('title')->nullable();
            $t->string('description', 255)->nullable();
            $t->string('keywords')->nullable();     // kommasepariert
            $t->json('schema_markup')->nullable();  // JSON-LD Rohdaten
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('seo_data');
    }
};
