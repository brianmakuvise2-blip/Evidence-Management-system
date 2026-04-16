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
        Schema::table('chain_of_custody', function (Blueprint $table) {
            if (!Schema::hasColumn('chain_of_custody', 'from_institution_id')) {
                $table->foreignId('from_institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            }
            if (!Schema::hasColumn('chain_of_custody', 'to_institution_id')) {
                $table->foreignId('to_institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            }
            if (!Schema::hasColumn('chain_of_custody', 'transfer_reason')) {
                $table->string('transfer_reason')->nullable();
            }
            if (!Schema::hasColumn('chain_of_custody', 'transfer_reference')) {
                $table->string('transfer_reference')->nullable()->unique();
            }
            if (!Schema::hasColumn('chain_of_custody', 'supervisor_approver_id')) {
                $table->foreignId('supervisor_approver_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('chain_of_custody', 'received_notes')) {
                $table->longText('received_notes')->nullable();
            }
            if (!Schema::hasColumn('chain_of_custody', 'notes')) {
                $table->longText('notes')->nullable();
            }
            if (!Schema::hasColumn('chain_of_custody', 'digital_signature')) {
                $table->string('digital_signature')->nullable();
            }
            if (!Schema::hasColumn('chain_of_custody', 'file_hash_at_transfer')) {
                $table->string('file_hash_at_transfer')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chain_of_custody', function (Blueprint $table) {
            if (Schema::hasColumn('chain_of_custody', 'from_institution_id')) {
                $table->dropForeignIdFor(\App\Models\Institution::class, 'from_institution_id');
            }
            if (Schema::hasColumn('chain_of_custody', 'to_institution_id')) {
                $table->dropForeignIdFor(\App\Models\Institution::class, 'to_institution_id');
            }
            if (Schema::hasColumn('chain_of_custody', 'transfer_reason')) {
                $table->dropColumn('transfer_reason');
            }
            if (Schema::hasColumn('chain_of_custody', 'transfer_reference')) {
                $table->dropColumn('transfer_reference');
            }
            if (Schema::hasColumn('chain_of_custody', 'supervisor_approver_id')) {
                $table->dropForeignIdFor(\App\Models\User::class, 'supervisor_approver_id');
            }
            if (Schema::hasColumn('chain_of_custody', 'received_notes')) {
                $table->dropColumn('received_notes');
            }
            if (Schema::hasColumn('chain_of_custody', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('chain_of_custody', 'digital_signature')) {
                $table->dropColumn('digital_signature');
            }
            if (Schema::hasColumn('chain_of_custody', 'file_hash_at_transfer')) {
                $table->dropColumn('file_hash_at_transfer');
            }
        });
    }
};
