<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('asset_tag')->unique();
            $table->string('asset_model');
            $table->string('assigned_user')->nullable();
            $table->string('location')->nullable();
            $table->string('rustdesk_id')->nullable();
            $table->text('windows_license_key')->nullable(); // encrypted via Eloquent cast
            $table->text('latest_updates_remarks')->nullable();
            $table->enum('accountability_signed', ['yes', 'pending'])->default('pending');
            $table->enum('accountability_uploaded_snipeit', ['yes', 'pending'])->default('pending');
            $table->date('date_issued')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('assigned_user');
            $table->index('location');
            $table->index(['accountability_signed', 'accountability_uploaded_snipeit'], 'assets_accountability_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
