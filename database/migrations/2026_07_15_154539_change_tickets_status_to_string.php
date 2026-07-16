<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Enum -> varchar so custom column keys can be stored.
        DB::statement("ALTER TABLE tickets MODIFY status VARCHAR(50) NOT NULL DEFAULT 'todo'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('todo','in_progress','done') NOT NULL DEFAULT 'todo'");
    }
};
