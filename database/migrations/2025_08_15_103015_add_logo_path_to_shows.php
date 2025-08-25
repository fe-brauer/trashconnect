<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shows', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('description'); // Speicherpfad auf dem "public" Disk
        });
    }
    public function down(): void {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};

