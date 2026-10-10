<?php

namespace App\Jobs;

use App\AI\ScopeReview\ScopeReviewGenerator;
use App\AI\ScopeReview\ScopeReviewInput;
use App\AI\ScopeReview\ScopeReviewValidationException;
use App\Enums\IntakeSubmissionStatus;
use App\Enums\ScopeReviewDraftStatus;
use App\Models\IntakeSubmission;
use App\Models\ScopeReviewDraft;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use LogicException;
use Throwable;

class GenerateScopeReviewDraft implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public function __construct(
        public readonly int $submissionId,
    ) {
    }

    public function handle(ScopeReviewGenerator $generator): void
    {
        $submission = IntakeSubmission::query()
            ->with('projectBrief')
            ->findOrFail($this->submissionId);

        if ($submission->status === IntakeSubmissionStatus::DraftReady) {
            return;
        }

        if (!in_array($submission->status, [
            IntakeSubmissionStatus::ReadyForAiReview,
            IntakeSubmissionStatus::Processing,
        ], true)) {
            throw new LogicException(
                "Submission {$submission->id} belum siap diproses."
            );
        }

        $draft = $this->markProcessing($submission);

        $input = new ScopeReviewInput(
            submissionId: $submission->id,
            organizationName: $submission->organization_name_snapshot,
            problemStatement: $submission->projectBrief->problem_statement,
            desiredOutcome: $submission->projectBrief->desired_outcome,
            constraints: $submission->projectBrief->constraints,
            additionalContext: $submission->projectBrief->additional_context,
        );

        try {
            $result = $generator->generate($input);
        } catch (ScopeReviewValidationException $exception) {
            $this->markFailed(
                $submission,
                $draft,
                ScopeReviewDraftStatus::FailedValidation,
                'failed_validation',
                'Output provider tidak lolos validasi kontrak.',
            );

            return;
        }

        DB::transaction(function () use ($submission, $draft, $result): void {
            $payload = $result->toArray();

            $draft->update([
                'contract_version' => $result->contractVersion,
                'status' => ScopeReviewDraftStatus::Generated,
                'draft_payload' => $payload,
                'provider_mode' => $result->generation['provider_mode'] ?? null,
                'prompt_revision' => $result->generation['prompt_revision'] ?? null,
                'error_code' => null,
                'error_message' => null,
                'completed_at' => now(),
            ]);

            $fromStatus = $submission->status;
            $submission->update([
                'status' => IntakeSubmissionStatus::DraftReady,
                'last_processed_at' => now(),
            ]);

            $submission->statusHistories()->create([
                'from_status' => $fromStatus,
                'to_status' => IntakeSubmissionStatus::DraftReady,
                'reason' => 'Draft scope review berhasil divalidasi.',
            ]);
        });
    }

    public function failed(?Throwable $exception): void
    {
        $submission = IntakeSubmission::query()->find($this->submissionId);

        if ($submission === null) {
            return;
        }

        $draft = ScopeReviewDraft::query()
            ->where('intake_submission_id', $submission->id)
            ->first();

        if ($draft === null) {
            $draft = ScopeReviewDraft::create([
                'intake_submission_id' => $submission->id,
            ]);
        }

        $this->markFailed(
            $submission,
            $draft,
            ScopeReviewDraftStatus::Failed,
            'provider_failed',
            'Pembuatan draft gagal setelah percobaan queue selesai.',
        );
    }

    private function markProcessing(IntakeSubmission $submission): ScopeReviewDraft
    {
        return DB::transaction(function () use ($submission): ScopeReviewDraft {
            $fromStatus = $submission->status;

            $submission->update([
                'status' => IntakeSubmissionStatus::Processing,
            ]);

            $submission->statusHistories()->create([
                'from_status' => $fromStatus,
                'to_status' => IntakeSubmissionStatus::Processing,
                'reason' => 'Job pembuatan draft dimulai.',
            ]);

            return ScopeReviewDraft::updateOrCreate(
                ['intake_submission_id' => $submission->id],
                [
                    'status' => ScopeReviewDraftStatus::Processing,
                    'draft_payload' => null,
                    'error_code' => null,
                    'error_message' => null,
                    'started_at' => now(),
                    'completed_at' => null,
                ],
            );
        });
    }

    private function markFailed(
        IntakeSubmission $submission,
        ScopeReviewDraft $draft,
        ScopeReviewDraftStatus $draftStatus,
        string $errorCode,
        string $safeMessage,
    ): void {
        DB::transaction(function () use (
            $submission,
            $draft,
            $draftStatus,
            $errorCode,
            $safeMessage,
        ): void {
            $draft->update([
                'status' => $draftStatus,
                'error_code' => $errorCode,
                'error_message' => $safeMessage,
                'completed_at' => now(),
            ]);

            $fromStatus = $submission->status;
            $submission->update([
                'status' => IntakeSubmissionStatus::Failed,
            ]);

            $submission->statusHistories()->create([
                'from_status' => $fromStatus,
                'to_status' => IntakeSubmissionStatus::Failed,
                'reason' => $safeMessage,
            ]);
        });
    }
}
