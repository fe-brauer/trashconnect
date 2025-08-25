<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('candidates', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('bio')->nullable();
            $t->date('birth_date')->nullable();
            $t->json('social_media')->nullable();
            $t->string('meta_title')->nullable();
            $t->string('meta_description', 255)->nullable();
            $t->timestamps();

            $t->index(['name', 'slug']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('candidates');
    }
};
