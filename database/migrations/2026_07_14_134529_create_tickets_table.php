<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();

            // 'support'   = technical support note / ticket
            // 'temp_issue'= temporarily issued asset (to be returned)
            $table->enum('type', ['support', 'temp_issue'])->default('support');
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->string('color', 20)->default('yellow');

            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('due_date')->nullable();
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
