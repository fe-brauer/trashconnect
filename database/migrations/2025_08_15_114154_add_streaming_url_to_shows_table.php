<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shows', function (Blueprint $table) {
            $table->string('streaming_url', 2048)->nullable()->after('network');
        });
    }

    public function down(): void {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropColumn('streaming_url');
        });
    }
};
