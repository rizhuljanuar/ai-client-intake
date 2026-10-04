<?php

use App\Enums\IntakeSubmissionStatus;
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
        Schema::create('intake_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_contact_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('contact_name_snapshot', 160);
            $table->string('contact_email_snapshot', 254);
            $table->string('organization_name_snapshot', 180)->nullable();
            $table->string('status', 40)->default(IntakeSubmissionStatus::Submitted->value);

            $table->timestampTz('submitted_at');
            $table->timestampTz('last_processed_at')->nullable();
            $table->timestampsTz();

            $table->index(['status', 'submitted_at']);
        });

        // SQLite (test database) cannot ADD/DROP CONSTRAINT on an existing
        // table, so the named CHECK constraint is Postgres-only.
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement(<<<'SQL'
                ALTER TABLE intake_submissions
                ADD CONSTRAINT intake_submissions_status_check
                CHECK (status IN (
                    'submitted',
                    'in_review',
                    'needs_clarification',
                    'ready_for_ai_review',
                    'processing',
                    'draft_ready',
                    'failed',
                    'closed'
                ))
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
                'ALTER TABLE intake_submissions DROP CONSTRAINT IF EXISTS intake_submissions_status_check'
            );
        }

        Schema::dropIfExists('intake_submissions');
    }
};
