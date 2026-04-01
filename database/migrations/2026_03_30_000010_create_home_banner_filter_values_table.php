<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_banner_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_definition_id')
                ->constrained('home_banner_filter_definitions')
                ->cascadeOnDelete();

            // Store as string so we can support both dropdown (string) and integer (casted) options.
            $table->string('value');
            $table->string('label')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('status')->default(true);
            $table->json('translations')->nullable();
            $table->timestamps();

            $table->unique(['filter_definition_id', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_banner_filter_values');
    }
};

