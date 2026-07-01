<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Populate the new lookup tables from existing free-text values.
        $models = DB::table('assets')
            ->whereNotNull('asset_model')->where('asset_model', '!=', '')
            ->distinct()->pluck('asset_model');
        foreach ($models as $name) {
            DB::table('asset_models')->updateOrInsert(
                ['name' => $name],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $locations = DB::table('assets')
            ->whereNotNull('location')->where('location', '!=', '')
            ->distinct()->pluck('location');
        foreach ($locations as $name) {
            DB::table('locations')->updateOrInsert(
                ['name' => $name],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 2. Add the foreign-key columns (nullable while we backfill).
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('model_id')->nullable()->after('asset_model')
                  ->constrained('asset_models')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->after('location')
                  ->constrained('locations')->nullOnDelete();
        });

        // 3. Backfill the new columns by matching on name (portable, no JOIN UPDATE).
        foreach (DB::table('asset_models')->get() as $m) {
            DB::table('assets')->where('asset_model', $m->name)->update(['model_id' => $m->id]);
        }
        foreach (DB::table('locations')->get() as $l) {
            DB::table('assets')->where('location', $l->name)->update(['location_id' => $l->id]);
        }

        // 4. Drop the old free-text columns (and the location index).
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['location']);
            $table->dropColumn(['asset_model', 'location']);
        });
    }

    public function down(): void
    {
        // Re-add the free-text columns and copy names back from the lookup tables.
        Schema::table('assets', function (Blueprint $table) {
            $table->string('asset_model')->nullable()->after('asset_tag');
            $table->string('location')->nullable()->after('assigned_user');
        });

        foreach (DB::table('asset_models')->get() as $m) {
            DB::table('assets')->where('model_id', $m->id)->update(['asset_model' => $m->name]);
        }
        foreach (DB::table('locations')->get() as $l) {
            DB::table('assets')->where('location_id', $l->id)->update(['location' => $l->name]);
        }

        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['model_id']);
            $table->dropColumn('model_id');
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
            $table->index('location');
        });
    }
};
