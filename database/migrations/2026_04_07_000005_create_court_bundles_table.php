<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('case_reference')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('prepared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->unsignedBigInteger('previous_version_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('case_reference');
            $table->index('status');
            $table->index('version');
        });

        Schema::create('court_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_bundle_id')->constrained('court_bundles')->onDelete('cascade');
            $table->foreignId('evidence_id')->constrained('evidence')->onDelete('cascade');
            $table->string('exhibit_number')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('page_reference')->default(1);
            $table->unsignedInteger('item_order')->default(1);
            $table->timestamps();

            $table->index(['court_bundle_id', 'item_order']);
            $table->index('evidence_id');
        });

        Schema::create('court_bundle_disclosures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_bundle_id')->constrained('court_bundles')->onDelete('cascade');
            $table->foreignId('shared_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_with_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('court_bundle_id');
            $table->index('shared_by_user_id');
            $table->index('shared_with_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('court_bundle_disclosures');
        Schema::dropIfExists('court_bundle_items');
        Schema::dropIfExists('court_bundles');
    }
};
