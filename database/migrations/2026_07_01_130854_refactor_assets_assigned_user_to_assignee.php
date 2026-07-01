<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add the polymorphic assignee columns (nullable).
        Schema::table('assets', function (Blueprint $table) {
            $table->string('assignee_type')->nullable()->after('serial_number');
            $table->unsignedBigInteger('assignee_id')->nullable()->after('assignee_type');
            $table->index(['assignee_type', 'assignee_id']);
        });

        // 2. Turn each existing free-text assigned_user into an Employee and point the asset at it.
        $names = DB::table('assets')
            ->whereNotNull('assigned_user')->where('assigned_user', '!=', '')
            ->distinct()->pluck('assigned_user');

        foreach ($names as $name) {
            DB::table('employees')->updateOrInsert(
                ['name' => $name],
                ['name' => $name, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        foreach (DB::table('employees')->get() as $emp) {
            DB::table('assets')
                ->where('assigned_user', $emp->name)
                ->update(['assignee_type' => 'employee', 'assignee_id' => $emp->id]);
        }

        // 3. Drop the old column (and its index).
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['assigned_user']);
            $table->dropColumn('assigned_user');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('assigned_user')->nullable()->after('serial_number');
        });

        // Copy employee names back for rows assigned to an employee.
        foreach (DB::table('employees')->get() as $emp) {
            DB::table('assets')
                ->where('assignee_type', 'employee')
                ->where('assignee_id', $emp->id)
                ->update(['assigned_user' => $emp->name]);
        }

        Schema::table('assets', function (Blueprint $table) {
            $table->index('assigned_user');
            $table->dropIndex(['assignee_type', 'assignee_id']);
            $table->dropColumn(['assignee_type', 'assignee_id']);
        });
    }
};
