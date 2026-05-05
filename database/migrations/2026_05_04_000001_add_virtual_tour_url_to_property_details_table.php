<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_details', function (Blueprint $table) {
            $table->string('virtual_tour_url', 2048)->nullable()->after('security_deposit');
        });
    }

    public function down(): void
    {
        Schema::table('property_details', function (Blueprint $table) {
            $table->dropColumn('virtual_tour_url');
        });
    }
};
