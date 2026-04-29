<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('designation')->nullable();
            $table->string('experience')->nullable();
            $table->text('description')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
