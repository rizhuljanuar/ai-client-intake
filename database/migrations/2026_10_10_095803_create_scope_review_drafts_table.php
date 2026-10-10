<?php

use App\Enums\ScopeReviewDraftStatus;
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
        Schema::create('scope_review_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_submission_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('contract_version', 80)->nullable();
            $table->string('status', 40)
                ->default(ScopeReviewDraftStatus::Processing->value);
            $table->jsonb('draft_payload')->nullable();
            $table->string('provider_mode', 40)->nullable();
            $table->string('prompt_revision', 120)->nullable();
            $table->string('error_code', 80)->nullable();
            $table->text('error_message')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampsTz();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scope_review_drafts');
    }
};
