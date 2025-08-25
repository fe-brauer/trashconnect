<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasColumn('shows', 'network')) {
            Schema::table('shows', function (Blueprint $table) {
                $table->dropColumn('network');
            });
        }
    }
    public function down(): void {
        Schema::table('shows', function (Blueprint $table) {
            $table->string('network')->nullable();
        });
    }
};
