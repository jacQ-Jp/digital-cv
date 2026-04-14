<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('cvs', 'personal_name')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->string('personal_name')->nullable()->after('summary');
            });
        }

        if (! Schema::hasColumn('cvs', 'personal_email')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->string('personal_email')->nullable()->after('personal_name');
            });
        }

        if (! Schema::hasColumn('cvs', 'photo_path')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->string('photo_path')->nullable()->after('personal_email');
            });
        }

        if (! Schema::hasColumn('cvs', 'public_uuid')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->uuid('public_uuid')->nullable()->after('status');
            });
        }

        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'cvs')
            ->where('index_name', 'cvs_public_uuid_unique')
            ->exists();

        if (! $indexExists && Schema::hasColumn('cvs', 'public_uuid')) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->unique('public_uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexExists = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'cvs')
            ->where('index_name', 'cvs_public_uuid_unique')
            ->exists();

        if ($indexExists) {
            Schema::table('cvs', function (Blueprint $table) {
                $table->dropUnique('cvs_public_uuid_unique');
            });
        }

        $dropColumns = [];
        foreach (['personal_name', 'personal_email', 'photo_path', 'public_uuid'] as $column) {
            if (Schema::hasColumn('cvs', $column)) {
                $dropColumns[] = $column;
            }
        }

        if ($dropColumns !== []) {
            Schema::table('cvs', function (Blueprint $table) use ($dropColumns) {
                $table->dropColumn($dropColumns);
            });
        }
    }
};
