<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('from_user')->nullable();
            $table->string('to_user');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->foreignId('transferred_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->date('transferred_at');
            $table->timestamps();

            $table->index('transferred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
