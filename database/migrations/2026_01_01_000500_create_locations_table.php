<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('city', 120);
            $table->string('state', 120)->nullable();
            $table->string('type', 20)->default('regional')->comment('corporate, plant, regional');
            $table->string('address', 500)->nullable()->comment('Only enter a verified address');
            $table->string('phone', 30)->nullable();
            $table->decimal('latitude', 9, 6)->nullable()->comment('City-level coordinate for the India map');
            $table->decimal('longitude', 9, 6)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
