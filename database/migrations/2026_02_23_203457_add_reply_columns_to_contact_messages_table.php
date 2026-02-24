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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->timestamp('replied_at')->nullable()->after('message');
            $table->text('reply_message')->nullable()->after('replied_at');
            $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete()->after('reply_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['replied_by']);
            $table->dropColumn(['replied_at', 'reply_message', 'replied_by']);
        });
    }
};
