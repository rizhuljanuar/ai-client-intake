<?php

namespace Tests\Feature;

use App\AI\ScopeReview\FakeScopeReviewGenerator;
use App\AI\ScopeReview\ScopeReviewOutputValidator;
use App\Enums\IntakeSubmissionStatus;
use App\Enums\ScopeReviewDraftStatus;
use App\Jobs\GenerateScopeReviewDraft;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateScopeReviewDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_stores_a_validated_draft_and_updates_submission_status(): void
    {
        $submission = $this->createReadySubmission();
        $generator = new FakeScopeReviewGenerator(
            new ScopeReviewOutputValidator(),
        );

        $job = new GenerateScopeReviewDraft($submission->id);
        $job->handle($generator);

        $submission->refresh();
        $draft = $submission->scopeReviewDraft()->first();

        $this->assertSame(
            IntakeSubmissionStatus::DraftReady,
            $submission->status,
        );
        $this->assertNotNull($draft);
        $this->assertSame(ScopeReviewDraftStatus::Generated, $draft->status);
        $this->assertSame('scope_review.v1', $draft->contract_version);
        $this->assertSame($submission->id, $draft->draft_payload['source_submission_id']);
        $this->assertDatabaseHas('submission_status_histories', [
            'intake_submission_id' => $submission->id,
            'to_status' => IntakeSubmissionStatus::DraftReady->value,
        ]);
    }

    private function createReadySubmission(): IntakeSubmission
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
            'status' => IntakeSubmissionStatus::ReadyForAiReview,
            'submitted_at' => now(),
        ]);

        $submission->projectBrief()->create([
            'problem_statement' => 'Reviewer membutuhkan tempat terpusat untuk menerima kebutuhan proyek.',
            'desired_outcome' => 'Reviewer dapat memeriksa kebutuhan sebelum diskusi lanjutan.',
        ]);

        return $submission;
    }
}
