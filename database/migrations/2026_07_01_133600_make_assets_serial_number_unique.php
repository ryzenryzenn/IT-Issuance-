<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalise blank serials to NULL so the unique index allows many "no serial" rows.
        DB::table('assets')->where('serial_number', '')->update(['serial_number' => null]);

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['serial_number']);   // drop the old non-unique index
            $table->unique('serial_number');        // enforce uniqueness (NULLs are exempt in MySQL)
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['serial_number']);
            $table->index('serial_number');
        });
    }
};
