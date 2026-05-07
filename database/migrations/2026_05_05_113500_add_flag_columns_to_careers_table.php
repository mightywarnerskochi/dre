<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            if (! Schema::hasColumn('careers', 'flag_image')) {
                $table->string('flag_image')->nullable()->after('country');
            }

            if (! Schema::hasColumn('careers', 'flag_alt')) {
                $table->string('flag_alt')->nullable()->after('flag_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            if (Schema::hasColumn('careers', 'flag_alt')) {
                $table->dropColumn('flag_alt');
            }

            if (Schema::hasColumn('careers', 'flag_image')) {
                $table->dropColumn('flag_image');
            }
        });
    }
};
