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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidence_id')->constrained('evidence')->cascadeOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users');
            $table->foreignId('receiving_officer_id')->nullable()->constrained('users');
            $table->foreignId('supervisor_approver_id')->nullable()->constrained('users');
            $table->foreignId('acknowledged_by_user_id')->nullable()->constrained('users');
            
            $table->string('transfer_reason');
            $table->integer('destination_institution_id');
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_transit', 'acknowledged', 'completed'])->default('pending');
            
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('in_transit_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('rejection_correction_notes')->nullable();
            $table->text('acknowledgment_notes')->nullable();
            
            $table->string('transfer_reference')->unique();
            $table->string('transfer_hash')->nullable();
            $table->string('digital_signature')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['evidence_id', 'status']);
            $table->index(['supervisor_approver_id', 'status']);
            $table->index(['acknowledged_by_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
