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
        Schema::create('updates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();     // Kurztext für Listen/Feed
            $table->longText('content')->nullable();   // RTE-HTML
            $table->string('cover_path')->nullable();  // optionales Cover (public disk)
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_public')->default(true);
            $table->timestamp('published_at')->nullable()->index();
            // (optional) “BugTracker”-Touch:
            $table->string('kind')->default('news');   // news|bug|change|feature (frei nutzbar)
            $table->string('status')->nullable();      // planned|in_progress|fixed|wontfix …
            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updates');
    }
};
