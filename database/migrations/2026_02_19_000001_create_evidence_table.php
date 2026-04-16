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
        Schema::create('evidence', function (Blueprint $table) {
            $table->id();

            // Case and Reference Information
            $table->string('case_reference')->nullable()->index();
            $table->string('title');
            $table->longText('description')->nullable();

            // Evidence Type and Classification
            $table->string('evidence_type')->default('physical'); // physical, digital, document, biological, forensic, trace, multimedia
            $table->string('status')->default('registered')->index(); // registered, verified, archived, rejected

            // Collection Information
            $table->dateTime('collected_date')->nullable();
            $table->foreignId('collected_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Verification Information
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('verified_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->longText('verification_notes')->nullable();

            // Digital File Information
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // mime type
            $table->bigInteger('file_size')->nullable(); // in bytes

            // Metadata (JSON for additional properties)
            $table->json('metadata')->nullable();

            // Institutional Information
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();
            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['status', 'evidence_type']);
            $table->index(['institution_id', 'department_id']);
            $table->index(['collected_date']);
            $table->fullText(['title', 'description', 'case_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
