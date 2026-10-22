<?php

use App\Enums\ScopeVersionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scope_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_submission_id')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('parent_version_id')
                ->nullable()
                ->constrained('scope_versions')
                ->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('status', 40)
                ->default(ScopeVersionStatus::Draft->value);
            $table->string('source_type', 40);
            $table->foreignId('source_draft_id')
                ->nullable()
                ->constrained('scope_review_drafts')
                ->restrictOnDelete();
            $table->jsonb('snapshot');
            $table->text('change_summary')->nullable();
            $table->nullableMorphs('created_by');
            $table->timestampsTz();

            $table->unique(['intake_submission_id', 'version_number']);
            $table->index(['intake_submission_id', 'status']);
        });

        DB::statement(<<<'SQL'
            ALTER TABLE scope_versions
            ADD CONSTRAINT scope_versions_status_check
            CHECK (status IN (
                'draft',
                'ready_for_approval',
                'approved',
                'rejected',
                'superseded',
                'withdrawn'
            ))
        SQL);

        DB::statement(<<<'SQL'
            ALTER TABLE scope_versions
            ADD CONSTRAINT scope_versions_source_check
            CHECK (source_type IN ('ai', 'reviewer', 'merge'))
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'ALTER TABLE scope_versions DROP CONSTRAINT IF EXISTS scope_versions_status_check'
        );

        DB::statement(
            'ALTER TABLE scope_versions DROP CONSTRAINT IF EXISTS scope_versions_source_check'
        );

        Schema::dropIfExists('scope_versions');}
};
