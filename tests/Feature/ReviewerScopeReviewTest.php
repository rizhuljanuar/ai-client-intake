<?php

namespace Tests\Feature;

use App\Enums\IntakeSubmissionStatus;
use App\Enums\ScopeReviewDraftStatus;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use App\Models\ScopeReviewDraft;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerScopeReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_draft_is_displayed_for_human_review(): void
    {
        $submission = $this->createSubmission();

        ScopeReviewDraft::create([
            'intake_submission_id' => $submission->id,
            'contract_version' => 'scope_review.v1',
            'status' => ScopeReviewDraftStatus::Generated,
            'draft_payload' => [
                'contract_version' => 'scope_review.v1',
                'source_submission_id' => $submission->id,
                'summary' => 'Ringkasan data uji untuk pemeriksaan reviewer.',
                'unclear_items' => [],
                'risks' => [],
                'assumptions' => [],
                'follow_up_questions' => [],
                'review_notes' => [],
                'generation' => [
                    'provider_mode' => 'fake',
                    'generated_at' => now()->toIso8601String(),
                    'prompt_revision' => 'scope-review-prompt.v1',
                ],
            ],
            'provider_mode' => 'fake',
            'prompt_revision' => 'scope-review-prompt.v1',
            'completed_at' => now(),
        ]);

        $response = $this->get(route('reviewer.submissions.draft.show', $submission));

        $response->assertOk();
        $response->assertSee('Ringkasan data uji untuk pemeriksaan reviewer.');
        $response->assertSee('Draft siap diperiksa');
        $response->assertSee('belum menjadi keputusan scope');
        $response->assertDontSee('Approve');
        $response->assertDontSee('Reject');
    }

    private function createSubmission(): IntakeSubmission
    {
        $contact = ClientContact::create([
            'name' => 'Data Uji',
            'email' => 'data-uji@example.test',
            'organization_name' => 'Organisasi Uji',
        ]);

        $submission = IntakeSubmission::create([
            'client_contact_id' => $contact->id,
            'contact_name_snapshot' => $contact->name,
            'contact_email_snapshot' => $contact->email,
            'organization_name_snapshot' => $contact->organization_name,
            'status' => IntakeSubmissionStatus::DraftReady,
            'submitted_at' => now(),
        ]);

        $submission->projectBrief()->create([
            'problem_statement' => 'Reviewer perlu membaca kebutuhan awal.',
            'desired_outcome' => 'Reviewer dapat memeriksa draft dengan sumber yang terlihat.',
        ]);

        return $submission;
    }

    public function test_processing_draft_shows_waiting_state(): void
    {
        $submission = $this->createSubmission();

        ScopeReviewDraft::create([
            'intake_submission_id' => $submission->id,
            'status' => ScopeReviewDraftStatus::Processing,
            'provider_mode' => 'fake',
            'started_at' => now(),
        ]);

        $response = $this->get(route('reviewer.submissions.draft.show', $submission));

        $response->assertOk();
        $response->assertSee('Draft sedang diproses');
        $response->assertDontSee('Hasil analisis awal');
    }

    public function test_missing_draft_shows_empty_state(): void
    {
        $submission = $this->createSubmission();

        $response = $this->get(route('reviewer.submissions.draft.show', $submission));

        $response->assertOk();
        $response->assertSee('Draft belum dibuat');
    }

    public function test_failed_draft_shows_safe_error_message(): void
    {
        $submission = $this->createSubmission();

        ScopeReviewDraft::create([
            'intake_submission_id' => $submission->id,
            'status' => ScopeReviewDraftStatus::FailedValidation,
            'error_code' => 'failed_validation',
            'error_message' => 'Output provider tidak lolos validasi kontrak.',
            'completed_at' => now(),
        ]);

        $response = $this->get(route('reviewer.submissions.draft.show', $submission));

        $response->assertOk();
        $response->assertSee('Draft belum dapat ditampilkan');
        $response->assertSee('failed_validation');
        $response->assertDontSee('Output provider tidak lolos validasi kontrak.');
    }
}
