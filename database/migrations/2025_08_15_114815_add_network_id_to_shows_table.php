<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::table('shows', function (Blueprint $table) {
            $table->foreignId('network_id')->nullable()->after('genre')->constrained()->nullOnDelete();
        });

        // Optionaler Backfill: alte 'network'-Strings in echte Datensätze übernehmen
        if (Schema::hasColumn('shows','network')) {
            $names = DB::table('shows')->select('network')->whereNotNull('network')->distinct()->pluck('network');
            foreach ($names as $name) {
                $id = DB::table('networks')->insertGetId([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'url'  => null,
                    'logo_path' => null,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                DB::table('shows')->where('network', $name)->update(['network_id' => $id]);
            }
        }

        // (Wenn du willst): alte Spalte jetzt entfernen
        // Schema::table('shows', fn (Blueprint $t) => $t->dropColumn('network'));
    }

    public function down(): void {
        // (Nur wenn du die alte Spalte behalten hast, hier nichts weiter)
        Schema::table('shows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('network_id');
        });
    }
};

