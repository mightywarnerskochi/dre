<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->unique();
            $table->text('access_token');
            $table->string('token_type')->default('Bearer');
            $table->unsignedInteger('expires_in')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_tokens');
    }
};
