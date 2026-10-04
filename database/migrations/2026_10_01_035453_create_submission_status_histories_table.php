<?php

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
        Schema::create('submission_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_submission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->nullableMorphs('actor');
            $table->text('reason')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['intake_submission_id', 'created_at']);
        });

        // SQLite (test database) cannot ADD/DROP CONSTRAINT on an existing
        // table, so the named CHECK constraint is Postgres-only.
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                ALTER TABLE submission_status_histories
                ADD CONSTRAINT submission_status_history_status_check
                CHECK (
                    (from_status IS NULL OR from_status IN (
                        'submitted', 'in_review', 'needs_clarification',
                        'ready_for_ai_review', 'processing', 'draft_ready',
                        'failed', 'closed'
                    ))
                    AND to_status IN (
                        'submitted', 'in_review', 'needs_clarification',
                        'ready_for_ai_review', 'processing', 'draft_ready',
                        'failed', 'closed'
                    )
                )
                SQL);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE submission_status_histories DROP CONSTRAINT IF EXISTS submission_status_history_status_check'
            );
        }

        Schema::dropIfExists('submission_status_histories');
    }
};
