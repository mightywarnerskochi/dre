<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_banner_filter_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('ui_type'); // dropdown | text | integer
            $table->json('translations')->nullable();
            $table->string('source_table')->nullable();
            $table->string('source_column')->nullable(); // supports comma-separated columns for combined sources

            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_banner_filter_definitions');
    }
};

