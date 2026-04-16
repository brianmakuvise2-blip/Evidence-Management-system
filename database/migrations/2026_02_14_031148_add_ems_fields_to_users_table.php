<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add EMS-specific fields
            $table->string('employee_id')->nullable()->unique()->after('id');
            $table->string('badge_number')->nullable()->unique()->after('employee_id');
            $table->foreignId('institution_id')->nullable()->constrained()->after('badge_number');
            $table->foreignId('department_id')->nullable()->constrained()->after('institution_id');
            $table->string('job_title')->nullable()->after('department_id');
            $table->string('phone_work')->nullable()->after('job_title');
            $table->string('phone_mobile')->nullable()->after('phone_work');
            $table->string('signature_path')->nullable()->after('phone_mobile');
            $table->string('profile_photo')->nullable()->after('signature_path');
            
            // Authentication and security
            $table->boolean('mfa_enabled')->default(false)->after('profile_photo');
            $table->string('mfa_secret')->nullable()->after('mfa_enabled');
            $table->json('mfa_recovery_codes')->nullable()->after('mfa_secret');
            $table->timestamp('last_login_at')->nullable()->after('mfa_recovery_codes');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->json('login_history')->nullable()->after('last_login_ip');
            
            // Account status
            $table->enum('account_status', ['active', 'inactive', 'suspended', 'archived'])->default('active')->after('login_history');
            $table->timestamp('archived_at')->nullable()->after('account_status');
            $table->foreignId('archived_by')->nullable()->constrained('users')->after('archived_at');
            $table->text('suspension_reason')->nullable()->after('archived_by');
            $table->timestamp('password_changed_at')->nullable()->after('suspension_reason');
            
            // Data access scope
            $table->enum('data_access_scope', ['personal', 'department', 'all'])->default('personal')->after('password_changed_at');
            
            $table->index(['institution_id', 'department_id', 'account_status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id',
                'badge_number',
                'institution_id',
                'department_id',
                'job_title',
                'phone_work',
                'phone_mobile',
                'signature_path',
                'profile_photo',
                'mfa_enabled',
                'mfa_secret',
                'mfa_recovery_codes',
                'last_login_at',
                'last_login_ip',
                'login_history',
                'account_status',
                'archived_at',
                'archived_by',
                'suspension_reason',
                'password_changed_at',
                'data_access_scope',
            ]);
            
            $table->dropForeign(['institution_id']);
            $table->dropForeign(['department_id']);
        });
    }
};