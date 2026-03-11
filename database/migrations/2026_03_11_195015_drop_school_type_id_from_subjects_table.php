<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('subjects', 'school_type_id')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique('subjects_name_school_type_id_unique');
            $table->dropConstrainedForeignId('school_type_id');
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'school_type_id')) {
                return;
            }

            $table->dropUnique('subjects_name_unique');
            $table->foreignId('school_type_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unique(['name', 'school_type_id']);
        });
    }
};
