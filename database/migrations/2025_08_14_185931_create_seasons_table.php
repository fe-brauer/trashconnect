<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seasons', function (Blueprint $t) {
            $t->id();
            $t->foreignId('show_id')->constrained()->cascadeOnDelete();
            $t->string('name');            // z.B. "Staffel 1"
            $t->string('slug');            // für /staffel/{show-slug}/{season-slug}
            $t->year('year')->nullable();
            $t->unsignedInteger('episode_count')->nullable();
            $t->string('meta_title')->nullable();
            $t->string('meta_description', 255)->nullable();
            $t->timestamps();

            $t->unique(['show_id', 'slug']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('seasons');
    }
};
