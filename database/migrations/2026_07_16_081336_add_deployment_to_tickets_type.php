<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY type ENUM('support','temp_issue','deployment') NOT NULL DEFAULT 'support'");
    }

    public function down(): void
    {
        // Fall deployment notes back to support before shrinking the enum.
        DB::table('tickets')->where('type', 'deployment')->update(['type' => 'support']);
        DB::statement("ALTER TABLE tickets MODIFY type ENUM('support','temp_issue') NOT NULL DEFAULT 'support'");
    }
};
