<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('download_records', function (Blueprint $table): void {
            $table->index('created_at', 'download_records_created_at_index');
        });

        Schema::table('documents', function (Blueprint $table): void {
            $table->index(['deleted_at', 'created_at'], 'documents_deleted_at_created_at_index');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->index(['role', 'created_at'], 'users_role_created_at_index');
        });

        if (Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->index(['role', 'last_login_at'], 'users_role_last_login_at_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('download_records', function (Blueprint $table): void {
            $table->dropIndex('download_records_created_at_index');
        });

        Schema::table('documents', function (Blueprint $table): void {
            $table->dropIndex('documents_deleted_at_created_at_index');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_role_created_at_index');
        });

        if (Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex('users_role_last_login_at_index');
            });
        }
    }
};
