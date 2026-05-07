<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('image_1')->nullable();
            $table->string('image_1_alt')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_2_alt')->nullable();
            $table->string('image_3')->nullable();
            $table->string('image_3_alt')->nullable();
            $table->string('image_4')->nullable();
            $table->string('image_4_alt')->nullable();
            $table->boolean('status')->default(true);
            $table->json('translations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
