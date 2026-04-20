<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('cvs', 'accent_color')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->string('accent_color', 7)->nullable()->after('photo_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cvs', 'accent_color')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->dropColumn('accent_color');
            });
        }
    }
};
