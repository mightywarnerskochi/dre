<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_nearby_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('place_id')->constrained('nearby_places')->cascadeOnDelete();
            $table->string('distance')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->unique(['property_id', 'place_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_nearby_places');
    }
};
