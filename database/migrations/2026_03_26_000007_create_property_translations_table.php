<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('address')->nullable();
            $table->string('full_address')->nullable();
            $table->string('city')->nullable();
            $table->string('community')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->json('easy_to_access')->nullable();
            $table->json('key_features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('property_attributes')->nullable();
            $table->unique(['property_id', 'language_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_translations');
    }
};
