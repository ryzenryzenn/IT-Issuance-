<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_columns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('color', 20)->default('gray');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        // Seed the three default columns.
        foreach ([['To Do', 'todo'], ['In Progress', 'in_progress'], ['Done', 'done']] as $i => [$name, $key]) {
            DB::table('board_columns')->insert([
                'name' => $name, 'key' => $key, 'color' => 'gray',
                'position' => $i, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_columns');
    }
};
