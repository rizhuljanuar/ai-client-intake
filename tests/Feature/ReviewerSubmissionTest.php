<?php

namespace Tests\Feature;

use App\Enums\IntakeSubmissionStatus;
use App\Models\ClientContact;
use App\Models\IntakeSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_submission_list_shows_empty_state(): void
    {
        $response = $this->get(route('reviewer.submissions.index'));

        $response->assertOk();
        $response->assertSee('Belum ada submission yang cocok');
    }

    public function test_submission_list_can_filter_by_valid_status(): void
    {
        $submitted = $this->createSubmission(IntakeSubmissionStatus::Submitted);
        $this->createSubmission(IntakeSubmissionStatus::InReview);

        $response = $this->get(route('reviewer.submissions.index', [
            'status' => IntakeSubmissionStatus::Submitted->value,
        ]));

        $response->assertOk();
        $response->assertSee($submitted->contact_name_snapshot);
        $response->assertDontSee('Data Uji In Review');
    }

    public function test_unknown_status_filter_does_not_add_an_untrusted_where_clause(): void
    {
        $submission = $this->createSubmission(IntakeSubmissionStatus::Submitted);

        $response = $this->get(route('reviewer.submissions.index', [
            'status' => 'status-yang-tidak-dikenal',
        ]));

        $response->assertOk();
        $response->assertSee($submission->contact_name_snapshot);
    }

    public function test_detail_page_loads_contact_brief_and_history(): void
    {
        $submission = $this->createSubmission(IntakeSubmissionStatus::Submitted);
        $submission->projectBrief()->create([
            'problem_statement' => 'Reviewer memerlukan detail kebutuhan.',
            'desired_outcome' => 'Reviewer dapat memeriksa isi intake.',
        ]);
        $submission->statusHistories()->create([
            'from_status' => null,
            'to_status' => IntakeSubmissionStatus::Submitted,
            'reason' => 'Form intake diterima',
        ]);

        $response = $this->get(route('reviewer.submissions.show', $submission));

        $response->assertOk();
        $response->assertSee('Reviewer memerlukan detail kebutuhan.');
        $response->assertSee('Form intake diterima');
    }

    public function test_missing_submission_returns_not_found(): void
    {
        $response = $this->get(route('reviewer.submissions.show', 999999));

        $response->assertNotFound();
    }

    private function createSubmission(IntakeSubmissionStatus $status): IntakeSubmission
    {
        $suffix = $status->value === IntakeSubmissionStatus::InReview->value
            ? ' In Review'
            : '';

        $contact = ClientContact::create([
            'name' => 'Data Uji' . $suffix,
            'email' => 'data-uji' . ($suffix === '' ? '' : '-review') . '@example.test',
            'organization_name' => 'Organisasi Uji',
        ]);

        return IntakeSubmission::create([
            'client_contact_id' => $contact->id,
            'contact_name_snapshot' => $contact->name,
            'contact_email_snapshot' => $contact->email,
            'organization_name_snapshot' => $contact->organization_name,
            'status' => $status,
            'submitted_at' => now(),
        ]);
    }
}
