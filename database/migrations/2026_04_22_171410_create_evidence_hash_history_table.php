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
        Schema::create('evidence_hash_history', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to evidence
            $table->foreignId('evidence_id')->constrained('evidence')->onDelete('cascade');
            
            // Hash information
            $table->string('hash_type')->default('sha256'); // sha256, md5, etc.
            $table->string('content_hash'); // Hash of the evidence content/data
            $table->string('metadata_hash')->nullable(); // Hash of metadata if applicable
            
            // Change tracking
            $table->string('change_type'); // 'created', 'updated', 'file_replaced', 'verified', 'accessed'
            $table->json('previous_state')->nullable(); // Previous hash and metadata
            $table->json('changed_fields')->nullable(); // What fields were changed
            
            // User and context
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('user_ip')->nullable();
            $table->string('user_agent')->nullable();
            
            // Verification status
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Tampering detection
            $table->boolean('tampering_detected')->default(false);
            $table->text('tampering_notes')->nullable();
            
            // Additional context
            $table->string('action')->nullable(); // Specific action performed
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['evidence_id', 'created_at']);
            $table->index(['hash_type', 'content_hash']);
            $table->index(['change_type']);
            $table->index(['is_verified']);
            $table->index(['tampering_detected']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence_hash_history');
    }
};
