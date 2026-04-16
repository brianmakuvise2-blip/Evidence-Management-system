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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('password_expires_at')->nullable()->after('password');
            $table->timestamp('last_password_change_at')->nullable()->after('password_expires_at');
            $table->integer('password_history_count')->default(0)->after('last_password_change_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password_expires_at', 'last_password_change_at', 'password_history_count']);
        });
    }
};
