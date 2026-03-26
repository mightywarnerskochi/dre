<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_translations', function (Blueprint $table) {
            $table->string('full_address')->nullable()->after('address');
            $table->string('city')->nullable()->after('full_address');
            $table->string('community')->nullable()->after('city');
            $table->string('country')->nullable()->after('community');
            $table->string('zip_code', 50)->nullable()->after('country');
            $table->json('easy_to_access')->nullable()->after('zip_code');
            $table->json('key_features')->nullable()->after('easy_to_access');
            $table->json('amenities')->nullable()->after('key_features');
            $table->json('property_attributes')->nullable()->after('amenities');
        });
    }

    public function down(): void
    {
        Schema::table('property_translations', function (Blueprint $table) {
            $table->dropColumn([
                'full_address',
                'city',
                'community',
                'country',
                'zip_code',
                'easy_to_access',
                'key_features',
                'amenities',
                'property_attributes',
            ]);
        });
    }
};
