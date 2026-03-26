<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('prop_id')->nullable()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('property_type')->nullable();
            $table->string('listing_type')->nullable();
            $table->enum('source_type', ['manual', 'sync'])->default('manual');
            $table->decimal('price', 15, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('sqft')->nullable();
            $table->string('address')->nullable();
            $table->string('full_address')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('community')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->boolean('status')->default(true);
            $table->unsignedInteger('order')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
