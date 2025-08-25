<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('url', 2048)->nullable();       // offizielle Website / Senderseite
            $table->string('logo_path')->nullable();       // Storage-Pfad (public disk)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('networks');
    }
};

