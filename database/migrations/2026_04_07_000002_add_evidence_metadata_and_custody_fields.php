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
        Schema::table('evidence', function (Blueprint $table) {
            // Check if columns don't already exist
            if (!Schema::hasColumn('evidence', 'exhibit_number')) {
                $table->string('exhibit_number')->nullable()->unique()->after('case_reference');
            }
            if (!Schema::hasColumn('evidence', 'source')) {
                $table->string('source')->nullable()->after('collected_date');
            }
            if (!Schema::hasColumn('evidence', 'location_found')) {
                $table->string('location_found')->nullable()->after('source');
            }
            if (!Schema::hasColumn('evidence', 'classification_level')) {
                $table->enum('classification_level', ['public', 'confidential', 'restricted', 'sealed'])->default('restricted')->after('location_found');
            }
            if (!Schema::hasColumn('evidence', 'file_hash')) {
                $table->string('file_hash')->nullable()->unique()->after('file_size');
            }
            if (!Schema::hasColumn('evidence', 'transferred_at')) {
                $table->timestamp('transferred_at')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('evidence', 'transferred_by_user_id')) {
                $table->foreignId('transferred_by_user_id')->nullable()->constrained('users')->after('transferred_at');
            }
            if (!Schema::hasColumn('evidence', 'disclosed_at')) {
                $table->timestamp('disclosed_at')->nullable()->after('transferred_by_user_id');
            }
            if (!Schema::hasColumn('evidence', 'disclosed_by_user_id')) {
                $table->foreignId('disclosed_by_user_id')->nullable()->constrained('users')->after('disclosed_at');
            }
            if (!Schema::hasColumn('evidence', 'disposed_at')) {
                $table->timestamp('disposed_at')->nullable()->after('disclosed_by_user_id');
            }
            if (!Schema::hasColumn('evidence', 'disposed_by_user_id')) {
                $table->foreignId('disposed_by_user_id')->nullable()->constrained('users')->after('disposed_at');
            }
            if (!Schema::hasColumn('evidence', 'disposal_reason')) {
                $table->text('disposal_reason')->nullable()->after('disposed_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            if (Schema::hasColumn('evidence', 'transferred_by_user_id')) {
                $table->dropForeign(['transferred_by_user_id']);
            }
            if (Schema::hasColumn('evidence', 'disclosed_by_user_id')) {
                $table->dropForeign(['disclosed_by_user_id']);
            }
            if (Schema::hasColumn('evidence', 'disposed_by_user_id')) {
                $table->dropForeign(['disposed_by_user_id']);
            }
            $table->dropColumn([
                'exhibit_number',
                'source',
                'location_found',
                'classification_level',
                'file_hash',
                'transferred_at',
                'transferred_by_user_id',
                'disclosed_at',
                'disclosed_by_user_id',
                'disposed_at',
                'disposed_by_user_id',
                'disposal_reason',
            ]);
        });
    }
};
