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
        Schema::table('property_messages', function (Blueprint $table) {
            $table->timestamp('sent_date')->nullable()->after('message');
            $table->enum('status', ['unread', 'read', 'replied'])->default('unread')->after('sent_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_messages', function (Blueprint $table) {
            $table->dropColumn(['sent_date', 'status']);
        });
    }
};
