<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('download_records', function (Blueprint $table) {
            $table->foreignId('document_file_id')
                ->nullable()
                ->after('document_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('download_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_file_id');
        });
    }
};
