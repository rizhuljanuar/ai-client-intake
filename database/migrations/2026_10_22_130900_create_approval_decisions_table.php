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
        Schema::create('approval_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scope_version_id')
                ->constrained('scope_versions')
                ->restrictOnDelete();
            $table->string('decision', 20);
            $table->text('note');
            $table->nullableMorphs('decided_by');
            $table->timestampTz('decided_at');
            $table->timestampsTz();

            $table->unique('scope_version_id');
        });

        DB::statement(<<<'SQL'
            ALTER TABLE approval_decisions
            ADD CONSTRAINT approval_decisions_decision_check
            CHECK (decision IN ('approved', 'rejected'))
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            'ALTER TABLE approval_decisions DROP CONSTRAINT IF EXISTS approval_decisions_decision_check'
        );

        Schema::dropIfExists('approval_decisions');
    }
};
