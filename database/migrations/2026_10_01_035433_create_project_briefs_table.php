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
        Schema::create('project_briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_submission_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->text('problem_statement');
            $table->text('desired_outcome');
            $table->text('constraints')->nullable();
            $table->text('additional_context')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_briefs');
    }
};
