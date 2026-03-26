<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->longText('description')->nullable();
            $table->unsignedSmallInteger('year_built')->nullable();
            $table->decimal('security_deposit', 15, 2)->nullable();
            $table->string('direct_from_owner')->nullable();
            $table->json('easy_to_access')->nullable();
            $table->json('key_features')->nullable();
            $table->json('amenities')->nullable();
            $table->json('property_attributes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_details');
    }
};
