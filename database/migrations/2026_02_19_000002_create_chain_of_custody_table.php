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
        Schema::create('chain_of_custody', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evidence_id')
                ->constrained('evidence')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id')->nullable();
            
            $table->foreign('from_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
                
            $table->foreign('to_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->dateTime('transferred_at')->nullable();
            $table->dateTime('received_at')->nullable();

            $table->string('location')->nullable();
            $table->string('purpose')->nullable();
            $table->longText('condition_notes')->nullable();

            $table->string('signature_from')->nullable();
            $table->string('signature_to')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['evidence_id', 'transferred_at']);
            $table->index(['from_user_id', 'to_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chain_of_custody');
    }
};
